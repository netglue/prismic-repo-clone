<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration\Factory;

use Prismic\Cloner\Migration\DocumentPatchTracker;
use Prismic\Cloner\PathConfig;
use Psr\Container\ContainerInterface;

final readonly class DocumentPatchTrackerFactory
{
    public function __invoke(ContainerInterface $container): DocumentPatchTracker
    {
        $paths = $container->get(PathConfig::class);

        return DocumentPatchTracker::fromFile($paths->patchTrackerFilePath());
    }
}
