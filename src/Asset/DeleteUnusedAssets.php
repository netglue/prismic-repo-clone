<?php

declare(strict_types=1);

namespace Prismic\Cloner\Asset;

use Prismic\Cloner\RepositoryContract;

final readonly class DeleteUnusedAssets
{
    public function __construct(
        private UsageTracker $usageTracker,
        private RepositoryContract $target,
    ) {
    }

    public function delete(): void
    {
        foreach ($this->usageTracker->unused() as $assetId) {
            $this->target->assetClient()->deleteAsset($assetId);
        }
    }
}
