<?php

declare(strict_types=1);

namespace Prismic\Cloner\DocumentType;

use Prismic\Cloner\PathConfig;
use Prismic\Cloner\SourceRepository;
use Psr\Container\ContainerInterface;

use function Psl\Filesystem\exists;

final readonly class DocumentTypesFactory
{
    public function __invoke(ContainerInterface $container): DocumentTypes
    {
        $paths = $container->get(PathConfig::class);
        $fileName = $paths->typeDefinitionsPath();

        if (exists($fileName)) {
            return DocumentTypes::fromFile($fileName);
        }

        $repo = $container->get(SourceRepository::class);

        $types = DocumentTypes::fromTypes(
            $repo->docTypeClient()->fetchAllDefinitions(),
        );
        $types->freezeTo($fileName);

        return $types;
    }
}
