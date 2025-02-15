<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration\Factory;

use Prismic\Cloner\Migration\DocumentMigrationTracker;
use Prismic\Cloner\PathConfig;
use Psr\Container\ContainerInterface;

final readonly class DocumentMigrationTrackerFactory
{
    public function __invoke(ContainerInterface $container): DocumentMigrationTracker
    {
        $paths = $container->get(PathConfig::class);

        return DocumentMigrationTracker::fromFile($paths->documentMigrationTrackerPath());
    }
}
