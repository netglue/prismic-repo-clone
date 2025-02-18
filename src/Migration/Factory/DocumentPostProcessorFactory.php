<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration\Factory;

use Prismic\Cloner\Migration\DocumentMigrationTracker;
use Prismic\Cloner\Migration\DocumentPatchTracker;
use Prismic\Cloner\Migration\DocumentPostProcessor;
use Prismic\Cloner\TargetRepository;
use Prismic\Cloner\Transformer\PostTransform;
use Psr\Container\ContainerInterface;

final readonly class DocumentPostProcessorFactory
{
    public function __invoke(ContainerInterface $container): DocumentPostProcessor
    {
        return new DocumentPostProcessor(
            $container->get(DocumentMigrationTracker::class),
            $container->get(TargetRepository::class),
            $container->get(PostTransform::class),
            $container->get(DocumentPatchTracker::class),
        );
    }
}
