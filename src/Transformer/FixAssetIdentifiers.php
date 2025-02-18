<?php

declare(strict_types=1);

namespace Prismic\Cloner\Transformer;

use Override;
use Prismic\Cloner\Asset\AssetMapper;
use Prismic\Cloner\Migration\MigrationDocumentHydrator;
use Prismic\Migration\Model\Document;

use function assert;
use function str_replace;

final readonly class FixAssetIdentifiers implements Transformer
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private AssetMapper $assetMapper,
    ) {
    }

    #[Override]
    public function transform(Document $document): Document
    {
        return MigrationDocumentHydrator::fromString(
            $this->migrateAssetsWithJsonString(
                MigrationDocumentHydrator::toString($document),
            ),
        );
    }

    /**
     * @param non-empty-string $json
     *
     * @return non-empty-string
     */
    private function migrateAssetsWithJsonString(string $json): string
    {
        // Ensure assets have been migrated
        ($this->assetMapper)();

        $out = $json;

        $map = $this->assetMapper->getMap();
        foreach ($map as $pair) {
            assert($pair->target !== null);
            $out = str_replace($pair->source->id, $pair->target->id, $out);
        }

        /** @psalm-var non-empty-string */

        return $out;
    }
}
