<?php

declare(strict_types=1);

namespace Prismic\Cloner;

use Prismic\Asset\Client as AssetClient;
use Prismic\DocumentType\Client as DocumentTypeClient;
use Prismic\DocumentType\SharedSliceManagementClient;

/** @psalm-api */
interface RepositoryContract
{
    /** @return non-empty-string */
    public function name(): string;

    public function assetClient(): AssetClient;

    public function docTypeClient(): DocumentTypeClient&SharedSliceManagementClient;
}
