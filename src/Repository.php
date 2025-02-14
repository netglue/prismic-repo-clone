<?php

declare(strict_types=1);

namespace Prismic\Cloner;

use Prismic\Asset\Client as AssetClient;

final readonly class Repository implements RepositoryContract
{
    /** @param non-empty-string $name */
    public function __construct(
        private string $name,
        private AssetClient $assetClient,
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
}
