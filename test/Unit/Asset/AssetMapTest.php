<?php

declare(strict_types=1);

namespace App\Test\Unit\Asset;

use BadMethodCallException;
use InvalidArgumentException;
use Override;
use PHPUnit\Framework\TestCase;
use Prismic\Cloner\Asset\AssetMap;
use Prismic\Cloner\Asset\AssetPair;

use function Psl\Filesystem\copy;
use function Psl\Filesystem\delete_file;
use function Psl\Filesystem\exists;

final class AssetMapTest extends TestCase
{
    /** @var non-empty-string */
    private string $workingPath;

    #[Override]
    protected function setUp(): void
    {
        $this->workingPath = __DIR__ . '/AssetMapFixtures/working.json';
    }

    #[Override]
    protected function tearDown(): void
    {
        if (! exists($this->workingPath)) {
            return;
        }

        delete_file($this->workingPath);
    }

    public function testSomeBasicBehaviour(): void
    {
        $map = AssetMap::fromFile(__DIR__ . '/AssetMapFixtures/example-map.json');
        self::assertSame(4, $map->count());
        self::assertSame(2, $map->countUnmapped());
        self::assertSame(1, $map->countFailures());
        self::assertTrue($map->isFailure($map->get('failed-item')->source), 'Should be a failure');
        self::assertFalse($map->isFailure($map->get('success-1')->source), 'This one was successfully mapped');
        self::assertFalse($map->isFailure($map->get('pending-1')->source), 'This one is not yet mapped');
    }

    public function testGetIsExceptional(): void
    {
        $map = AssetMap::fromFile(__DIR__ . '/AssetMapFixtures/example-map.json');
        $this->expectException(InvalidArgumentException::class);
        $map->get('foo');
    }

    public function testMappingASourceRemovesItFromFailures(): void
    {
        copy(__DIR__ . '/AssetMapFixtures/example-map.json', $this->workingPath);
        $map = AssetMap::fromFile($this->workingPath);

        $pair = $map->get('failed-item');
        $map->map($pair->source, $pair->source);

        self::assertFalse($map->isFailure($pair->source));
    }

    public function testYouCannotFailASuccessfullyMappedResource(): void
    {
        $map = AssetMap::fromFile(__DIR__ . '/AssetMapFixtures/example-map.json');
        $pair = $map->get('success-1');
        $this->expectException(BadMethodCallException::class);
        $map->fail($pair->source);
    }

    public function testNextSourceIsExpectedItem(): void
    {
        $map = AssetMap::fromFile(__DIR__ . '/AssetMapFixtures/example-map.json');
        $next = $map->nextUnMappedSource(false);
        self::assertNotNull($next);
        self::assertSame('pending-1', $next->id);
    }

    public function testFailuresAreNotSkippedWhenRetryIsTrue(): void
    {
        $map = AssetMap::fromFile(__DIR__ . '/AssetMapFixtures/example-map.json');
        $next = $map->nextUnMappedSource(true);
        self::assertNotNull($next);
        self::assertSame('failed-item', $next->id);
    }

    public function testFailingASourceAddsItToFailures(): void
    {
        copy(__DIR__ . '/AssetMapFixtures/example-map.json', $this->workingPath);
        $map = AssetMap::fromFile($this->workingPath);
        $pair = $map->get('pending-1');
        $map->fail($pair->source);

        self::assertTrue($map->isFailure($pair->source));
        self::assertSame(2, $map->countFailures());
    }

    public function testWhenEverythingIsMappedOrFailedTheNextSourceIsNull(): void
    {
        copy(__DIR__ . '/AssetMapFixtures/example-map.json', $this->workingPath);
        $map = AssetMap::fromFile($this->workingPath);
        $pair = $map->get('pending-1');
        $map->fail($pair->source);

        self::assertNull($map->nextUnMappedSource(false));
        self::assertNotNull($map->nextUnMappedSource(true));
    }

    public function testListenersAreTriggeredAfterMapping(): void
    {
        copy(__DIR__ . '/AssetMapFixtures/example-map.json', $this->workingPath);
        $map = AssetMap::fromFile($this->workingPath);
        $pair = $map->get('pending-1');

        $checkVariable = null;

        $listener = static function (AssetPair $mappedPair) use (&$checkVariable): void {
            $checkVariable = $mappedPair;
        };

        $map->registerListener($listener);

        $map->map($pair->source, $pair->source);

        self::assertInstanceOf(AssetPair::class, $checkVariable);
    }

    public function testMapWillBeCreatedFromAList(): void
    {
        $data = AssetMap::fromFile(__DIR__ . '/AssetMapFixtures/example-map.json');
        $list = [
            $data->get('success-1')->source,
            $data->get('success-2')->source,
        ];

        self::assertFalse(exists($this->workingPath));

        $map = AssetMap::fromAssetList($list, $this->workingPath);

        self::assertTrue(exists($this->workingPath));

        self::assertSame(2, $map->count());
    }
}
