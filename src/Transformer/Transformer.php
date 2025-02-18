<?php

declare(strict_types=1);

namespace Prismic\Cloner\Transformer;

use Prismic\Migration\Model\Document;

interface Transformer
{
    public function transform(Document $document): Document;
}
