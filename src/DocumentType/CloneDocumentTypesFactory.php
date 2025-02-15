<?php

declare(strict_types=1);

namespace Prismic\Cloner\DocumentType;

use Prismic\Cloner\PathConfig;
use Prismic\Cloner\SourceRepository;
use Prismic\Cloner\TargetRepository;
use Psr\Container\ContainerInterface;

final readonly class CloneDocumentTypesFactory
{
    public function __invoke(ContainerInterface $container): CloneDocumentTypes
    {
        $paths = $container->get(PathConfig::class);

        return new CloneDocumentTypes(
            $container->get(SourceRepository::class),
            $container->get(TargetRepository::class),
            $container->get(DocumentTypes::class),
            $paths->typeDefProgressPath(),
        );
    }
}
