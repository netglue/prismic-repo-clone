<?php

declare(strict_types=1);

namespace Prismic\Cloner\Asset;

use ArrayIterator;
use BadMethodCallException;
use Closure;
use CuyZ\Valinor\Mapper\Source\JsonSource;
use CuyZ\Valinor\MapperBuilder;
use InvalidArgumentException;
use IteratorAggregate;
use Override;
use Prismic\Asset\Model\Asset;
use Psl\File\WriteMode;
use Traversable;

use function array_key_exists;
use function count;
use function Psl\File\read;
use function Psl\File\write;
use function Psl\Json\encode;

/**
 * @implements IteratorAggregate<string, AssetPair>
 * @psalm-api
 */
final class AssetMap implements IteratorAggregate
{
    /**
     * @param array<string, AssetPair>       $map
     * @param array<string, Asset>           $failures
     * @param non-empty-string               $file
     * @param list<Closure(AssetPair): void> $listeners
     */
    public function __construct(
        private array $map,
        private array $failures,
        private readonly string $file,
        private array $listeners = [],
    ) {
    }

    /**
     * @param list<Asset>      $list
     * @param non-empty-string $targetFile
     */
    public static function fromAssetList(array $list, string $targetFile): self
    {
        $map = [];
        foreach ($list as $asset) {
            $map[$asset->id] = new AssetPair($asset, null);
        }

        $mapper = new self($map, [], $targetFile);
        $mapper->freeze();

        return $mapper;
    }

    /** @param non-empty-string $file */
    public static function fromFile(string $file): self
    {
        $map = (new MapperBuilder())
            ->enableFlexibleCasting()
            ->allowPermissiveTypes()
            ->mapper()
            ->map(
                'array{failures: array<string, ' . Asset::class . '>, map: array<string, ' . AssetPair::class . '>}',
                new JsonSource(read($file)),
            );

        return new self($map['map'], $map['failures'], $file);
    }

    /** @param Closure(AssetPair): void $listener */
    public function registerListener(Closure $listener): void
    {
        $this->listeners[] = $listener;
    }

    public function map(Asset $source, Asset $target): void
    {
        $assetPair = new AssetPair($source, $target);
        $this->map[$source->id] = $assetPair;
        unset($this->failures[$source->id]);
        $this->freeze();
        foreach ($this->listeners as $listener) {
            $listener($assetPair);
        }
    }

    public function fail(Asset $source): void
    {
        if ($this->isSuccess($source->id)) {
            throw new BadMethodCallException('The asset ' . $source->id . 'has already been successfully mapped');
        }

        $this->failures[$source->id] = $source;
        $this->freeze();
    }

    public function get(string $id): AssetPair
    {
        if (! isset($this->map[$id])) {
            throw new InvalidArgumentException('Asset not found: ' . $id);
        }

        return $this->map[$id];
    }

    private function freeze(): void
    {
        write(
            $this->file,
            encode(['failures' => $this->failures, 'map' => $this->map], true),
            WriteMode::Truncate,
        );
    }

    public function isFailure(Asset $asset): bool
    {
        return array_key_exists($asset->id, $this->failures);
    }

    public function isSuccess(string $id): bool
    {
        $pair = $this->get($id);

        return $pair->target !== null;
    }

    public function nextUnMappedSource(bool $retryFailures = false): Asset|null
    {
        foreach ($this->map as $pair) {
            if ($pair->target !== null) {
                continue;
            }

            if ($retryFailures === false && $this->isFailure($pair->source)) {
                continue;
            }

            return $pair->source;
        }

        return null;
    }

    public function count(): int
    {
        return count($this->map);
    }

    public function countUnmapped(): int
    {
        $c = 0;
        foreach ($this->map as $pair) {
            if ($pair->target !== null) {
                continue;
            }

            $c++;
        }

        return $c;
    }

    public function countFailures(): int
    {
        return count($this->failures);
    }

    /** @return Traversable<string, AssetPair> */
    #[Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->map);
    }
}
