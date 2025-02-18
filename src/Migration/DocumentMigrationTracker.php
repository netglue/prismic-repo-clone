<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration;

use ArrayIterator;
use BadMethodCallException;
use Countable;
use IteratorAggregate;
use Override;
use Psl\File\WriteMode;
use Traversable;

use function array_key_exists;
use function count;
use function Psl\File\read;
use function Psl\File\write;
use function Psl\Filesystem\exists;
use function Psl\Json\encode;
use function Psl\Json\typed;
use function Psl\Type\dict;
use function Psl\Type\non_empty_string;
use function Psl\Type\null;
use function Psl\Type\union;
use function sprintf;

/** @implements IteratorAggregate<non-empty-string, non-empty-string|null> */
final class DocumentMigrationTracker implements IteratorAggregate, Countable
{
    /**
     * @param non-empty-string                               $filePath
     * @param array<non-empty-string, non-empty-string|null> $map
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
            $map = typed(read($filePath), dict(non_empty_string(), union(non_empty_string(), null())));

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
        return array_key_exists($sourceId, $this->map) && $this->map[$sourceId] !== null;
    }

    /**
     * @param non-empty-string $sourceId
     *
     * @return non-empty-string
     */
    public function getTarget(string $sourceId): string
    {
        if (! $this->isMigrated($sourceId)) {
            throw new BadMethodCallException(sprintf(
                '"%s" has not been migrated yet',
                $sourceId,
            ));
        }

        return non_empty_string()->assert($this->map[$sourceId]);
    }

    /** @param non-empty-string $sourceId */
    public function registerSource(string $sourceId): void
    {
        if (array_key_exists($sourceId, $this->map)) {
            throw new BadMethodCallException(sprintf(
                'Document "%s" is already registered',
                $sourceId,
            ));
        }

        $this->map[$sourceId] = null;
        $this->freeze();
    }

    /** @param non-empty-string $sourceId */
    public function isRegistered(string $sourceId): bool
    {
        return array_key_exists($sourceId, $this->map);
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

    /** @return Traversable<non-empty-string, non-empty-string|null> */
    #[Override]
    public function getIterator(): Traversable
    {
        /** @phpstan-ignore return.type */
        return new ArrayIterator($this->map);
    }

    #[Override]
    public function count(): int
    {
        return count($this->map);
    }
}
