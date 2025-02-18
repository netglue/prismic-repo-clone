<?php

declare(strict_types=1);

namespace Prismic\Cloner\Transformer;

use Psr\Container\ContainerInterface;

final readonly class PreTransformFactory
{
    public function __invoke(ContainerInterface $container): ChainTransformer
    {
        return new ChainTransformer(
            $container->get(FixAssetIdentifiers::class),
            new RemoveEmptyStructs(),
        );
    }
}
