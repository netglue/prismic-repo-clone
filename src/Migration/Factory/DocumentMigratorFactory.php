<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration\Factory;

use GSteel\Dot;
use Prismic\Cloner\Migration\DocumentMigrationTracker;
use Prismic\Cloner\Migration\DocumentMigrator;
use Prismic\Cloner\Migration\TitleResolver;
use Prismic\Cloner\SourceRepository;
use Prismic\Cloner\TargetRepository;
use Prismic\Cloner\Transformer\PreTransform;
use Psr\Container\ContainerInterface;

use function Psl\Type\mixed_dict;
use function sprintf;

final readonly class DocumentMigratorFactory
{
    public function __invoke(ContainerInterface $container): DocumentMigrator
    {
        $config = mixed_dict()->assert($container->get('config'));
        $targetRepo = $container->get(TargetRepository::class);

        $forceLanguage = Dot::stringOrNull(
            sprintf(
                'repositories.%s.forceLanguage',
                $targetRepo->name(),
            ),
            $config,
        );

        $forceLanguage = $forceLanguage === '' ? null : $forceLanguage;

        return new DocumentMigrator(
            $container->get(DocumentMigrationTracker::class),
            $container->get(SourceRepository::class),
            $targetRepo,
            $container->get(TitleResolver::class),
            $container->get(PreTransform::class),
            $forceLanguage,
        );
    }
}
