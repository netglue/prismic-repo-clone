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
use Prismic\Cloner\Asset\CopyAssetFactory;
use Prismic\Cloner\Asset\DeleteUnusedAssets;
use Prismic\Cloner\Asset\DeleteUnusedAssetsFactory;
use Prismic\Cloner\Asset\UsageTracker;
use Prismic\Cloner\Asset\UsageTrackerFactory;
use Prismic\Cloner\DocumentType\CloneDocumentTypes;
use Prismic\Cloner\DocumentType\CloneDocumentTypesFactory;
use Prismic\Cloner\DocumentType\DocumentTypes;
use Prismic\Cloner\DocumentType\DocumentTypesFactory;
use Prismic\Cloner\Factory\PathConfigFactory;
use Prismic\Cloner\Migration\DefaultTitleResolver;
use Prismic\Cloner\Migration\DocumentMigrationTracker;
use Prismic\Cloner\Migration\DocumentMigrator;
use Prismic\Cloner\Migration\DocumentPatchTracker;
use Prismic\Cloner\Migration\DocumentPostProcessor;
use Prismic\Cloner\Migration\Factory\DocumentMigrationTrackerFactory;
use Prismic\Cloner\Migration\Factory\DocumentMigratorFactory;
use Prismic\Cloner\Migration\Factory\DocumentPatchTrackerFactory;
use Prismic\Cloner\Migration\Factory\DocumentPostProcessorFactory;
use Prismic\Cloner\Migration\Factory\TitleResolverFactory;
use Prismic\Cloner\Migration\ResolveSinglesToTypeLabel;
use Prismic\Cloner\Migration\TitleResolver;
use Prismic\Cloner\PathConfig;
use Prismic\Cloner\Transformer\AdjustInternalLinks;
use Prismic\Cloner\Transformer\FixAssetIdentifiers;
use Prismic\Cloner\Transformer\PostTransform;
use Prismic\Cloner\Transformer\PostTransformFactory;
use Prismic\Cloner\Transformer\PreTransform;
use Prismic\Cloner\Transformer\PreTransformFactory;

// phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly

return [
    'dependencies' => [
        'factories' => [
            // Utils, generic deps
            CurlClient::class => InvokableFactory::class,
            FinfoMimeTypeDetector::class => InvokableFactory::class,

            // Main tooling services
            PathConfig::class => PathConfigFactory::class,
            CopyAsset::class => CopyAssetFactory::class,
            AssetMapper::class => AssetMapperFactory::class,
            CloneDocumentTypes::class => CloneDocumentTypesFactory::class,
            DocumentTypes::class => DocumentTypesFactory::class,
            DocumentMigrationTracker::class => DocumentMigrationTrackerFactory::class,
            DocumentMigrator::class => DocumentMigratorFactory::class,
            UsageTracker::class => UsageTrackerFactory::class,
            DeleteUnusedAssets::class => DeleteUnusedAssetsFactory::class,

            // Tools for figuring out the document title
            DefaultTitleResolver::class => ReflectionBasedAbstractFactory::class,
            ResolveSinglesToTypeLabel::class => ReflectionBasedAbstractFactory::class,
            TitleResolver::class => TitleResolverFactory::class,

            // Tools for pre- and post-processing the documents
            FixAssetIdentifiers::class => ReflectionBasedAbstractFactory::class,
            AdjustInternalLinks::class => ReflectionBasedAbstractFactory::class,
            PreTransform::class => PreTransformFactory::class,
            PostTransform::class => PostTransformFactory::class,
            DocumentPostProcessor::class => DocumentPostProcessorFactory::class,
            DocumentPatchTracker::class => DocumentPatchTrackerFactory::class,
        ],
        'aliases' => [
            Psr\Http\Client\ClientInterface::class => CurlClient::class,
            MimeTypeDetector::class => FinfoMimeTypeDetector::class,
        ],
    ],
];
