<?php

declare(strict_types=1);

namespace Prismic\Cloner;

use Prismic\Asset\Client as AssetClient;
use Prismic\DocumentType\Client as DocumentTypeClient;
use Prismic\DocumentType\SharedSliceManagementClient;
use Prismic\Migration\DocumentClient;
use Prismic\Migration\MigrationClient;

final readonly class Repository implements RepositoryContract
{
    /** @param non-empty-string $name */
    public function __construct(
        private string $name,
        private AssetClient $assetClient,
        private DocumentTypeClient&SharedSliceManagementClient $docTypeClient,
        private DocumentClient $documentClient,
        private MigrationClient $migrationClient,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function assetClient(): AssetClient
    {
        return $this->assetClient;
    }

    public function docTypeClient(): DocumentTypeClient&SharedSliceManagementClient
    {
        return $this->docTypeClient;
    }

    public function documentClient(): DocumentClient
    {
        return $this->documentClient;
    }

    public function migrationClient(): MigrationClient
    {
        return $this->migrationClient;
    }
}
