<?php

declare(strict_types=1);

namespace Prismic\Cloner\DocumentType;

use GSteel\Dot;
use Prismic\DocumentType\BaseClient;
use Prismic\DocumentType\Client;
use Prismic\DocumentType\SharedSliceManagementClient;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

use function Psl\Type\mixed_dict;
use function sprintf;

final readonly class DocumentTypeClientFactory
{
    /** @param non-empty-string $name */
    public static function create(ContainerInterface $container, string $name): Client&SharedSliceManagementClient
    {
        $config = mixed_dict()->assert($container->get('config'));

        $token = Dot::nonEmptyString(
            sprintf('repositories.%s.writeToken', $name),
            $config,
        );

        return new BaseClient(
            $token,
            $name,
            $container->get(ClientInterface::class),
            $container->get(RequestFactoryInterface::class),
            $container->get(UriFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
        );
    }
}
