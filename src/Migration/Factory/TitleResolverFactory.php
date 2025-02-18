<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration\Factory;

use Prismic\Cloner\Migration\DefaultTitleResolver;
use Prismic\Cloner\Migration\ResolveSinglesToTypeLabel;
use Prismic\Cloner\Migration\TitleResolver;
use Prismic\Cloner\Migration\TitleResolverChain;
use Psr\Container\ContainerInterface;

final readonly class TitleResolverFactory
{
    public function __invoke(ContainerInterface $container): TitleResolver
    {
        return new TitleResolverChain(
            $container->get(DefaultTitleResolver::class),
            $container->get(ResolveSinglesToTypeLabel::class),
        );
    }
}
