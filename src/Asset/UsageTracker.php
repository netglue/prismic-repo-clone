<?php

declare(strict_types=1);

namespace Prismic\Cloner\Asset;

use Psl\File\WriteMode;

use function array_key_exists;
use function Psl\File\read;
use function Psl\File\write;
use function Psl\Filesystem\exists;
use function Psl\Json\encode;
use function Psl\Json\typed;
use function Psl\Type\dict;
use function Psl\Type\int;
use function Psl\Type\non_empty_string;

final class UsageTracker
{
    /**
     * @param non-empty-string             $filePath
     * @param array<non-empty-string, int> $counts
     */
    private function __construct(
        private readonly string $filePath,
        private array $counts,
    ) {
    }

    /** @param non-empty-string $file */
    public static function fromFile(string $file): self
    {
        if (! exists($file)) {
            $self = new self($file, []);
            $self->freeze();

            return $self;
        }

        return new self(
            $file,
            /** @phpstan-ignore-next-line */
            typed(read($file), dict(non_empty_string(), int())),
        );
    }

    /** @param non-empty-string $assetId */
    public function incrementUsageCount(string $assetId): void
    {
        if (! array_key_exists($assetId, $this->counts)) {
            $this->counts[$assetId] = 0;
        }

        $this->counts[$assetId]++;
        $this->freeze();
    }

    private function freeze(): void
    {
        write(
            $this->filePath,
            encode($this->counts, true),
            WriteMode::Truncate,
        );
    }

    /** @return list<non-empty-string> */
    public function unused(): array
    {
        $list = [];
        foreach ($this->counts as $asset => $count) {
            if ($count > 0) {
                continue;
            }

            $list[] = $asset;
        }

        return $list;
    }
}
