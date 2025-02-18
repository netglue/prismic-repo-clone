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

use function count;
use function in_array;
use function Psl\File\read;
use function Psl\File\write;
use function Psl\Filesystem\exists;
use function Psl\Json\encode;
use function Psl\Json\typed;
use function Psl\Type\non_empty_string;
use function Psl\Type\vec;
use function sprintf;

/** @implements IteratorAggregate<int, non-empty-string> */
final class DocumentPatchTracker implements IteratorAggregate, Countable
{
    /**
     * @param non-empty-string       $filePath
     * @param list<non-empty-string> $list
     */
    private function __construct(
        private readonly string $filePath,
        private array $list,
    ) {
    }

    /** @param non-empty-string $filePath */
    public static function fromFile(string $filePath): self
    {
        if (exists($filePath)) {
            $map = typed(read($filePath), vec(non_empty_string()));

            return new self($filePath, $map);
        }

        $self = new self($filePath, []);
        $self->freeze();

        return $self;
    }

    /** @param non-empty-string $targetId */
    public function isPatched(string $targetId): bool
    {
        return in_array($targetId, $this->list, true);
    }

    /** @param non-empty-string $targetId */
    public function registerPatch(string $targetId): void
    {
        if ($this->isPatched($targetId)) {
            throw new BadMethodCallException(sprintf(
                'Document "%s" is already patched',
                $targetId,
            ));
        }

        $this->list[] = $targetId;
        $this->freeze();
    }

    private function freeze(): void
    {
        write(
            $this->filePath,
            encode($this->list, true),
            WriteMode::Truncate,
        );
    }

    /** @return Traversable<int, non-empty-string> */
    #[Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->list);
    }

    #[Override]
    public function count(): int
    {
        return count($this->list);
    }
}
