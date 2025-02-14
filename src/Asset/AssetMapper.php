<?php

declare(strict_types=1);

namespace Prismic\Cloner\Asset;

use Prismic\Asset\Model\Asset;
use Prismic\Cloner\RepositoryContract;
use Throwable;

use function file_exists;

final readonly class AssetMapper
{
    /**
     * @param non-empty-string $sourceListFile
     * @param non-empty-string $assetMapFile
     */
    public function __construct(
        private RepositoryContract $source,
        private RepositoryContract $target,
        private CopyAsset $copyAsset,
        private string $sourceListFile,
        private string $assetMapFile,
    ) {
    }

    public function __invoke(bool $retryFailures = false): void
    {
        $map = $this->getMap();

        $source = $map->nextUnMappedSource($retryFailures);

        while ($source !== null) {
            try {
                $copy = $this->copyAsset->copy($source, $this->target->assetClient());
                $map->map($source, $copy);
            } catch (Throwable) {
                $map->fail($source);
            }

            $source = $map->nextUnMappedSource($retryFailures);
        }
    }

    public function getMap(): AssetMap
    {
        if (! file_exists($this->assetMapFile)) {
            return AssetMap::fromAssetList(
                $this->fetchAssetList(),
                $this->assetMapFile,
            );
        }

        return AssetMap::fromFile($this->assetMapFile);
    }

    /** @return list<Asset> */
    private function fetchAssetList(): array
    {
        $list = new AssetList($this->source, $this->sourceListFile);

        return $list->fetch();
    }
}
