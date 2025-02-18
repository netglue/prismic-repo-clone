<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration;

use Prismic\Cloner\RepositoryContract;
use Prismic\Cloner\Transformer\Transformer;
use Prismic\Migration\Exception\AssetNotFound;
use Prismic\Migration\Exception\GenericRequestFailure;
use Prismic\Migration\Exception\RateLimitExceeded;
use Prismic\Migration\Model\Document;
use Prismic\Migration\Model\MigrationDocumentPatch;

use function Psl\Type\non_empty_string;
use function sleep;
use function sprintf;

use const PHP_EOL;

final readonly class DocumentPostProcessor
{
    public function __construct(
        private DocumentMigrationTracker $tracker,
        private RepositoryContract $target,
        private Transformer $postTransformer,
        private DocumentPatchTracker $patchTracker,
    ) {
    }

    public function process(): void
    {
        foreach ($this->tracker as $targetId) {
            if (! non_empty_string()->matches($targetId)) {
                continue;
            }

            if ($this->patchTracker->isPatched($targetId)) {
                continue;
            }

            $document = $this->postTransformer->transform(
                $this->target->getDocumentState($targetId),
            );

            try {
                $this->patch($document);
            } catch (RateLimitExceeded) {
                sleep(1);
                $this->patch($document);
            }
        }
    }

    private function patch(Document $document): void
    {
        $patch = new MigrationDocumentPatch(
            $document->id,
            $document->uid,
            $document->data,
            $document->tags,
        );

        try {
            $this->target->migrationClient()->updateDocument($patch);
        } catch (RateLimitExceeded | AssetNotFound $error) {
            throw $error;
        } catch (GenericRequestFailure $error) {
            $message = sprintf(
                'Failed to perform post-migration on document "%s". (UID: %s, Type: %s)%s%s',
                $document->id,
                $document->uid ?? '[none]',
                $document->type,
                PHP_EOL,
                $error->getMessage(),
            );

            throw new DocumentMigrationFailure($message, $error->getCode(), $error);
        }

        $this->patchTracker->registerPatch($document->id);
        $this->target->persistDocumentState($document);
    }
}
