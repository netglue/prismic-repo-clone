<?php

declare(strict_types=1);

namespace Prismic\Cloner\Factory;

use GSteel\Dot;
use Prismic\Asset\AssetClient;
use Prismic\Asset\Client;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

use function Psl\Type\instance_of;
use function Psl\Type\mixed_dict;
use function sprintf;

final readonly class AssetClientFactory
{
    /** @param non-empty-string $name */
    public function __construct(private string $name)
    {
    }

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

    /**
     * @param non-empty-string $name
     * @param list<mixed>      $arguments
     *
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function __callStatic(string $name, array $arguments): Client
    {
        return (new self($name))(
            instance_of(ContainerInterface::class)->assert(
                $arguments[0] ?? null,
            ),
        );
    }

    public function __invoke(ContainerInterface $container): Client
    {
        $config = mixed_dict()->assert($container->get('config'));

        $token = Dot::nonEmptyString(
            sprintf('repositories.%s.writeToken', $this->name),
            $config,
        );

        return new AssetClient(
            $token,
            $this->name,
            $container->get(ClientInterface::class),
            $container->get(RequestFactoryInterface::class),
            $container->get(UriFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
        );
    }
}
