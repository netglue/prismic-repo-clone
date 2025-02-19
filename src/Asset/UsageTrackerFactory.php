<?php

declare(strict_types=1);

namespace Prismic\Cloner\Asset;

use Prismic\Cloner\PathConfig;
use Psr\Container\ContainerInterface;

final readonly class UsageTrackerFactory
{
    public function __invoke(ContainerInterface $container): UsageTracker
    {
        $paths = $container->get(PathConfig::class);

        return UsageTracker::fromFile($paths->assetUsageFilePath());
    }
}
