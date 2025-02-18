<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration;

use Prismic\Cloner\RepositoryContract;
use Prismic\Cloner\Transformer\Transformer;
use Prismic\Migration\Exception\AssetNotFound;
use Prismic\Migration\Exception\GenericRequestFailure;
use Prismic\Migration\Exception\RateLimitExceeded;
use Prismic\Migration\Model\Document;
use Prismic\Migration\Model\MigrationDocument;

use function sleep;
use function sprintf;

use const PHP_EOL;

final readonly class DocumentMigrator
{
    /** @param non-empty-string|null $forceLanguage */
    public function __construct(
        private DocumentMigrationTracker $tracker,
        private RepositoryContract $source,
        private RepositoryContract $target,
        private TitleResolver $resolver,
        private Transformer $preTransform,
        private string|null $forceLanguage,
    ) {
    }

    public function migrate(): void
    {
        foreach ($this->source->fetchDocuments() as $document) {
            try {
                $this->migrateDocument($document);
            } catch (RateLimitExceeded) {
                sleep(1);
                $this->migrateDocument($document);
            }
        }
    }

    private function migrateDocument(Document $document): void
    {
        if (! $this->tracker->isRegistered($document->id)) {
            $this->tracker->registerSource($document->id);
        }

        if ($this->tracker->isMigrated($document->id)) {
            return;
        }

        $document = $this->preTransform->transform($document);

        $migration = new MigrationDocument(
            $this->resolver->resolve($document) ?? '',
            $document->type,
            $document->uid,
            $this->forceLanguage ?? $document->lang,
            $document->data,
        );

        try {
            $result = $this->target->migrationClient()->createDocument($migration);
        } catch (RateLimitExceeded | AssetNotFound $error) {
            throw $error;
        } catch (GenericRequestFailure $e) {
            $message = sprintf(
                'Failed to migrate document "%s". (UID: %s, Type: %s)%s%s',
                $document->id,
                $document->uid ?? '[none]',
                $document->type,
                PHP_EOL,
                $e->getMessage(),
            );

            throw new DocumentMigrationFailure($message, $e->getCode(), $e);
        }

        $this->tracker->migrate($document->id, $result->id);

        /**
         * Persist the changed document under the target repository
         * so that it can be loaded and adjusted once all documents have been migrated
         */
        $this->target->persistDocumentState(new Document(
            $result->id,
            $migration->uid,
            $migration->type,
            $migration->lang,
            $document->tags,
            $migration->data,
        ));
    }
}
