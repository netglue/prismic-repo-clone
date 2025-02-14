<?php

declare(strict_types=1);

namespace Prismic\Cloner;

use Prismic\Asset\Client as AssetClient;
use Prismic\DocumentType\Client as DocumentTypeClient;
use Prismic\DocumentType\SharedSliceManagementClient;

final readonly class Repository implements RepositoryContract
{
    /** @param non-empty-string $name */
    public function __construct(
        private string $name,
        private AssetClient $assetClient,
        private DocumentTypeClient&SharedSliceManagementClient $docTypeClient,
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
}
