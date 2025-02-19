<?php

declare(strict_types=1);

namespace Prismic\Cloner\Asset;

use Prismic\Cloner\TargetRepository;
use Psr\Container\ContainerInterface;

final readonly class DeleteUnusedAssetsFactory
{
    public function __invoke(ContainerInterface $container): DeleteUnusedAssets
    {
        return new DeleteUnusedAssets(
            $container->get(UsageTracker::class),
            $container->get(TargetRepository::class),
        );
    }
}
