<?php

declare(strict_types=1);

namespace Prismic\Cloner\Transformer;

use Override;
use Prismic\Cloner\Migration\DocumentMigrationTracker;
use Prismic\Cloner\Migration\MigrationDocumentHydrator;
use Prismic\Migration\Model\Document;

use function str_replace;

final readonly class AdjustInternalLinks implements Transformer
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private DocumentMigrationTracker $tracker,
    ) {
    }

    #[Override]
    public function transform(Document $document): Document
    {
        return MigrationDocumentHydrator::fromString(
            $this->transformLinks(
                MigrationDocumentHydrator::toString($document),
            ),
        );
    }

    /**
     * @param non-empty-string $json
     *
     * @return non-empty-string
     */
    private function transformLinks(string $json): string
    {
        $out = $json;
        foreach ($this->tracker as $sourceId => $targetId) {
            if ($targetId === null) {
                continue;
            }

            $out = str_replace($sourceId, $targetId, $out);
        }

        /** @psalm-var non-empty-string */

        return $out;
    }
}
