<?php

declare(strict_types=1);

namespace Prismic\Cloner\Factory;

use GSteel\Dot;
use Prismic\Cloner\PathConfig;
use Psr\Container\ContainerInterface;

use function Psl\Type\mixed_dict;

final readonly class PathConfigFactory
{
    public function __invoke(ContainerInterface $container): PathConfig
    {
        $config = mixed_dict()->assert($container->get('config'));

        $paths = new PathConfig(
            Dot::nonEmptyString('app.data-directory', $config),
            Dot::nonEmptyString('app.asset-map-filename', $config),
            Dot::nonEmptyString('app.asset-list-filename', $config),
            Dot::nonEmptyString('app.type-definition-progress-filename', $config),
            Dot::nonEmptyString('app.type-definitions-filename', $config),
        );

        $paths->createDirectories();

        return $paths;
    }
}
