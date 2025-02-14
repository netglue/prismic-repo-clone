<?php

declare(strict_types=1);

namespace Prismic\Cloner;

use Prismic\Asset\Client as AssetClient;

/** @psalm-api */
interface RepositoryContract
{
    /** @return non-empty-string */
    public function name(): string;

    public function assetClient(): AssetClient;
}
