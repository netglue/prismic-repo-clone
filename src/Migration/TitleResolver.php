<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration;

use Prismic\Migration\Model\Document;

interface TitleResolver
{
    /** @return non-empty-string|null */
    public function resolve(Document $document): string|null;
}
