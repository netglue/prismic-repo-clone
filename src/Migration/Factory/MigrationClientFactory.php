<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration\Factory;

use GSteel\Dot;
use Prismic\Migration\MigrationClient;
use Prismic\Migration\MigrationClientImplementation;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

use function Psl\Type\array_key;
use function Psl\Type\dict;
use function Psl\Type\mixed_dict;
use function Psl\Type\non_empty_string;
use function shuffle;
use function sprintf;

final readonly class MigrationClientFactory
{
    /** @param non-empty-string $repositoryName */
    public static function create(ContainerInterface $container, string $repositoryName): MigrationClient
    {
        $config = mixed_dict()->assert($container->get('config'));

        $token = Dot::nonEmptyString(
            sprintf('repositories.%s.writeToken', $repositoryName),
            $config,
        );

        $keys = dict(array_key(), non_empty_string())->assert(
            Dot::array('migrationApiKeys', $config),
        );
        shuffle($keys);

        return new MigrationClientImplementation(
            $token,
            $repositoryName,
            $keys[0],
            $container->get(ClientInterface::class),
            $container->get(RequestFactoryInterface::class),
            $container->get(UriFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
        );
    }
}
