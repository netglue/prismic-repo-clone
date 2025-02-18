<?php

declare(strict_types=1);

namespace Prismic\Cloner\DocumentType;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Override;
use Prismic\DocumentType\Definition;
use Psl\File\WriteMode;
use Traversable;

use function array_map;
use function count;
use function Psl\File\read;
use function Psl\File\write;
use function Psl\Json\encode;
use function Psl\Json\typed;
use function Psl\Type\mixed_dict;
use function Psl\Type\vec;
use function Psl\Vec\values;
use function sprintf;
use function strtolower;

/**
 * @implements IteratorAggregate<int, Definition>
 * @psalm-api
 */
final readonly class DocumentTypes implements IteratorAggregate, Countable
{
    /** @param list<Definition> $types */
    private function __construct(
        private array $types,
    ) {
    }

    /** @param non-empty-string $id */
    public function get(string $id): Definition
    {
        foreach ($this as $definition) {
            if (strtolower($id) === strtolower($definition->id())) {
                return $definition;
            }
        }

        throw new InvalidArgumentException(sprintf(
            'Definition "%s" not found',
            $id,
        ));
    }

    /** @param non-empty-string $filePath */
    public static function fromFile(string $filePath): self
    {
        $data = typed(
            read($filePath),
            vec(mixed_dict()),
        );

        return new self(array_map(
            static fn (array $type): Definition => Definition::fromArray($type),
            $data,
        ));
    }

    /** @param iterable<array-key, Definition> $types */
    public static function fromTypes(iterable $types): self
    {
        return new self(values($types));
    }

    /** @param non-empty-string $filePath */
    public function freezeTo(string $filePath): void
    {
        write(
            $filePath,
            encode($this->types, true),
            WriteMode::Truncate,
        );
    }

    /** @return Traversable<int, Definition> */
    #[Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->types);
    }

    #[Override]
    public function count(): int
    {
        return count($this->types);
    }
}
