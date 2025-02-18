<?php

declare(strict_types=1);

use Prismic\Cloner\Asset\AssetMapper;
use Prismic\Cloner\DocumentType\CloneDocumentTypes;
use Prismic\Cloner\Migration\DocumentMigrator;
use Prismic\Cloner\Migration\DocumentPostProcessor;
use Psr\Container\ContainerInterface;

$container = require_once __DIR__ . '/../config/container.php';

assert($container instanceof ContainerInterface);

$assetMapper = $container->get(AssetMapper::class);
$assetMapper(true);

$typeCloner = $container->get(CloneDocumentTypes::class);
$typeCloner();

$documentMigrator = $container->get(DocumentMigrator::class);
$documentMigrator->migrate();

$postProcessor = $container->get(DocumentPostProcessor::class);
$postProcessor->process();
