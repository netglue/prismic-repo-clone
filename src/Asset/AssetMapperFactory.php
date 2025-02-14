<?php

declare(strict_types=1);

namespace Prismic\Cloner\Asset;

use Prismic\Cloner\PathConfig;
use Prismic\Cloner\SourceRepository;
use Prismic\Cloner\TargetRepository;
use Psr\Container\ContainerInterface;

final readonly class AssetMapperFactory
{
    public function __invoke(ContainerInterface $container): AssetMapper
    {
        $paths = $container->get(PathConfig::class);

        return new AssetMapper(
            $container->get(SourceRepository::class),
            $container->get(TargetRepository::class),
            $container->get(CopyAsset::class),
            $paths->assetListPath(),
            $paths->assetMapPath(),
        );
    }
}
