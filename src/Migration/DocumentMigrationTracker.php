<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration;

use ArrayIterator;
use BadMethodCallException;
use IteratorAggregate;
use Psl\File\WriteMode;
use Traversable;

use function array_key_exists;
use function Psl\File\read;
use function Psl\File\write;
use function Psl\Filesystem\exists;
use function Psl\Json\encode;
use function Psl\Json\typed;
use function Psl\Type\dict;
use function Psl\Type\non_empty_string;
use function sprintf;

/** @implements IteratorAggregate<non-empty-string, non-empty-string> */
final class DocumentMigrationTracker implements IteratorAggregate
{
    /**
     * @param non-empty-string                          $filePath
     * @param array<non-empty-string, non-empty-string> $map
     */
    private function __construct(
        private readonly string $filePath,
        private array $map,
    ) {
    }

    /** @param non-empty-string $filePath */
    public static function fromFile(string $filePath): self
    {
        if (exists($filePath)) {
            $map = typed(read($filePath), dict(non_empty_string(), non_empty_string()));

            /** @phpstan-ignore argument.type */
            return new self($filePath, $map);
        }

        $self = new self($filePath, []);
        $self->freeze();

        return $self;
    }

    /** @param non-empty-string $sourceId */
    public function isMigrated(string $sourceId): bool
    {
        return array_key_exists($sourceId, $this->map);
    }

    /**
     * @param non-empty-string $sourceId
     *
     * @return non-empty-string
     */
    public function getTarget(string $sourceId): string
    {
        $target = $this->map[$sourceId] ?? null;
        if ($target !== null) {
            return $target;
        }

        throw new BadMethodCallException(sprintf(
            '"%s" has not been migrated yet',
            $sourceId,
        ));
    }

    /**
     * @param non-empty-string $sourceId
     * @param non-empty-string $targetId
     */
    public function migrate(string $sourceId, string $targetId): void
    {
        if ($this->isMigrated($sourceId)) {
            throw new BadMethodCallException(sprintf(
                '"%s" has already been migrated',
                $sourceId,
            ));
        }

        $this->map[$sourceId] = $targetId;
        $this->freeze();
    }

    private function freeze(): void
    {
        write(
            $this->filePath,
            encode($this->map, true),
            WriteMode::Truncate,
        );
    }

    /** @return Traversable<non-empty-string, non-empty-string> */
    public function getIterator(): Traversable
    {
        /** @phpstan-ignore return.type */
        return new ArrayIterator($this->map);
    }
}
