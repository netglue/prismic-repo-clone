<?php

declare(strict_types=1);

namespace Prismic\Cloner\Asset;

use GSteel\Dot;
use League\MimeTypeDetection\MimeTypeDetector;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

use function Psl\Type\dict;
use function Psl\Type\mixed_dict;
use function Psl\Type\non_empty_string;
use function Psl\Type\vec;

final readonly class CopyAssetFactory
{
    public function __invoke(ContainerInterface $container): CopyAsset
    {
        $config = mixed_dict()->assert($container->get('config'));

        return new CopyAsset(
            $container->get(ClientInterface::class),
            $container->get(RequestFactoryInterface::class),
            $container->get(UriFactoryInterface::class),
            $container->get(MimeTypeDetector::class),
            /** @phpstan-ignore-next-line */
            dict(non_empty_string(), vec(non_empty_string()))->assert(Dot::array('assetTagMap', $config)),
            Dot::bool('searchAssetAltTextForTags', $config),
            Dot::bool('searchAssetNotesForTags', $config),
        );
    }
}
