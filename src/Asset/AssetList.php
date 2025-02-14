<?php

declare(strict_types=1);

namespace Prismic\Cloner\Asset;

use CuyZ\Valinor\Mapper\Source\JsonSource;
use CuyZ\Valinor\MapperBuilder;
use Prismic\Asset\Exception\ApiError;
use Prismic\Asset\Model\Asset;
use Prismic\Cloner\RepositoryContract;
use Psl\File\WriteMode;
use RuntimeException;
use Throwable;

use function file_exists;
use function Psl\File\read;
use function Psl\File\write;
use function Psl\Json\encode;
use function sprintf;

final readonly class AssetList
{
    /** @param non-empty-string $filePath */
    public function __construct(
        private RepositoryContract $sourceRepository,
        private string $filePath,
    ) {
    }

    /** @return list<Asset> */
    public function fetch(): array
    {
        if (! file_exists($this->filePath)) {
            return $this->fetchFromRemote();
        }

        return $this->fetchFromFile();
    }

    /** @return list<Asset> */
    private function fetchFromRemote(): array
    {
        try {
            $list = $this->sourceRepository->assetClient()->listAssets();
        } catch (ApiError $error) {
            throw new RuntimeException(
                'Failed to fetch the asset list from the remote repository',
                $error->getCode(),
                $error,
            );
        }

        write(
            $this->filePath,
            encode($list, true),
            WriteMode::Truncate,
        );

        return $list;
    }

    /** @return list<Asset> */
    private function fetchFromFile(): array
    {
        try {
            return (new MapperBuilder())
                ->enableFlexibleCasting()
                ->allowPermissiveTypes()
                ->mapper()
                ->map(
                    'list<' . Asset::class . '>',
                    new JsonSource(read($this->filePath)),
                );
        } catch (Throwable $e) {
            throw new RuntimeException(
                sprintf(
                    'Failed to process the cached asset list in %s. Manually remove this file to try again',
                    $this->filePath,
                ),
                (int) $e->getCode(),
                $e,
            );
        }
    }
}
