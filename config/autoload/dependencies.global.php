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
use Prismic\Cloner\DocumentType\DocumentTypes;
use Prismic\Cloner\DocumentType\DocumentTypesFactory;
use Prismic\Cloner\Factory\PathConfigFactory;
use Prismic\Cloner\Migration\DefaultTitleResolver;
use Prismic\Cloner\Migration\DocumentMigrationTracker;
use Prismic\Cloner\Migration\Factory\DocumentMigrationTrackerFactory;
use Prismic\Cloner\Migration\TitleResolver;
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
            DocumentTypes::class => DocumentTypesFactory::class,
            DocumentMigrationTracker::class => DocumentMigrationTrackerFactory::class,
            DefaultTitleResolver::class => ReflectionBasedAbstractFactory::class,
        ],
        'aliases' => [
            Psr\Http\Client\ClientInterface::class => CurlClient::class,
            MimeTypeDetector::class => FinfoMimeTypeDetector::class,
            TitleResolver::class => DefaultTitleResolver::class,
        ],
    ],
];
