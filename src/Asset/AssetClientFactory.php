<?php

declare(strict_types=1);

namespace Prismic\Cloner\Asset;

use GSteel\Dot;
use Prismic\Asset\AssetClient;
use Prismic\Asset\Client;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

use function Psl\Type\mixed_dict;
use function sprintf;

final readonly class AssetClientFactory
{
    /** @param non-empty-string $name */
    public static function create(ContainerInterface $container, string $name): Client
    {
        $config = mixed_dict()->assert($container->get('config'));

        $token = Dot::nonEmptyString(
            sprintf('repositories.%s.writeToken', $name),
            $config,
        );

        return new AssetClient(
            $token,
            $name,
            $container->get(ClientInterface::class),
            $container->get(RequestFactoryInterface::class),
            $container->get(UriFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
        );
    }
}
