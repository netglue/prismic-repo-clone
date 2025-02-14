<?php

declare(strict_types=1);

use Http\Client\Curl\Client as CurlClient;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use League\MimeTypeDetection\MimeTypeDetector;
use Prismic\Cloner\Asset\AssetMapper;
use Prismic\Cloner\Asset\AssetMapperFactory;
use Prismic\Cloner\Asset\CopyAsset;
use Prismic\Cloner\DocumentType\CloneDocumentTypes;
use Prismic\Cloner\DocumentType\CloneDocumentTypesFactory;
use Prismic\Cloner\Factory\PathConfigFactory;
use Prismic\Cloner\PathConfig;

// phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly

return [
    'dependencies' => [
        'factories' => [
            CurlClient::class => InvokableFactory::class,
            FinfoMimeTypeDetector::class => InvokableFactory::class,
            CopyAsset::class => ReflectionBasedAbstractFactory::class,
            PathConfig::class => PathConfigFactory::class,
            AssetMapper::class => AssetMapperFactory::class,
            CloneDocumentTypes::class => CloneDocumentTypesFactory::class,
        ],
        'aliases' => [
            Psr\Http\Client\ClientInterface::class => CurlClient::class,
            MimeTypeDetector::class => FinfoMimeTypeDetector::class,
        ],
    ],
];
