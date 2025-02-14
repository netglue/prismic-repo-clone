<?php

declare(strict_types=1);

namespace Prismic\Cloner\Asset;

use Prismic\Asset\Model\Asset;

final readonly class AssetPair
{
    public function __construct(
        public Asset $source,
        public Asset|null $target,
    ) {
    }
}
