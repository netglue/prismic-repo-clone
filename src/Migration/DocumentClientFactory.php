<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration;

use GSteel\Dot;
use Prismic\Migration\DocumentClient;
use Prismic\Migration\DocumentClientImplementation;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

use function Psl\Type\mixed_dict;
use function sprintf;

final readonly class DocumentClientFactory
{
    /** @param non-empty-string $repositoryName */
    public static function create(ContainerInterface $container, string $repositoryName): DocumentClient
    {
        $config = mixed_dict()->assert($container->get('config'));

        $token = Dot::stringOrNull(sprintf(
            'repositories.%s.readToken',
            $repositoryName,
        ), $config);

        $token = $token === null || $token === '' ? null : $token;

        return new DocumentClientImplementation(
            $token,
            $repositoryName,
            $container->get(ClientInterface::class),
            $container->get(RequestFactoryInterface::class),
            $container->get(UriFactoryInterface::class),
        );
    }
}
