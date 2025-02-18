<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration;

use RuntimeException;

final class RateLimitExceeded extends RuntimeException
{
}
