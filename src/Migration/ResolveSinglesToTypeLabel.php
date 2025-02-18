<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration;

use Override;
use Prismic\Cloner\DocumentType\DocumentTypes;
use Prismic\Migration\Model\Document;

final readonly class ResolveSinglesToTypeLabel implements TitleResolver
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private DocumentTypes $types,
    ) {
    }

    #[Override]
    public function resolve(Document $document): string|null
    {
        $type = $this->types->get($document->type);

        if ($type->isRepeatable()) {
            return null;
        }

        return $type->label() === '' ? null : $type->label();
    }
}
