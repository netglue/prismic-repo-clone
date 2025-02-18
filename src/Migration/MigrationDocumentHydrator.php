<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration;

use CuyZ\Valinor\Mapper\Source\JsonSource;
use CuyZ\Valinor\MapperBuilder;
use Prismic\Migration\Model\Document;

use function Psl\Json\encode;

final readonly class MigrationDocumentHydrator
{
    /** @return non-empty-string */
    public static function toString(Document $document): string
    {
        return encode($document, true);
    }

    public static function fromString(string $json): Document
    {
        return (new MapperBuilder())
            ->allowPermissiveTypes()
            ->allowSuperfluousKeys()
            ->enableFlexibleCasting()
            ->mapper()
            ->map(Document::class, new JsonSource($json));
    }
}
