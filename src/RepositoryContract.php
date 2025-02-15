<?php

declare(strict_types=1);

namespace Prismic\Cloner;

use Prismic\Asset\Client as AssetClient;
use Prismic\DocumentType\Client as DocumentTypeClient;
use Prismic\DocumentType\SharedSliceManagementClient;
use Prismic\Migration\DocumentClient;
use Prismic\Migration\MigrationClient;

/** @psalm-api */
interface RepositoryContract
{
    /** @return non-empty-string */
    public function name(): string;

    public function assetClient(): AssetClient;

    public function docTypeClient(): DocumentTypeClient&SharedSliceManagementClient;

    public function documentClient(): DocumentClient;

    public function migrationClient(): MigrationClient;
}
