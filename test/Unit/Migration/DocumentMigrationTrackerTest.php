<?php

declare(strict_types=1);

namespace App\Test\Unit\Migration;

use BadMethodCallException;
use Override;
use PHPUnit\Framework\TestCase;
use Prismic\Cloner\Migration\DocumentMigrationTracker;

use function iterator_to_array;
use function Psl\Filesystem\delete_file;
use function Psl\Filesystem\exists;

final class DocumentMigrationTrackerTest extends TestCase
{
    /** @var non-empty-string */
    private string $workingPath;

    #[Override]
    protected function setUp(): void
    {
        $this->workingPath = __DIR__ . '/working.json';
    }

    #[Override]
    protected function tearDown(): void
    {
        if (! exists($this->workingPath)) {
            return;
        }

        delete_file($this->workingPath);
    }

    public function testTheFileWillBeCreatedImmediately(): void
    {
        self::assertFileDoesNotExist($this->workingPath);
        DocumentMigrationTracker::fromFile($this->workingPath);
        self::assertFileExists($this->workingPath);
    }

    public function testIsMigrated(): void
    {
        $tracker = DocumentMigrationTracker::fromFile($this->workingPath);
        self::assertFalse($tracker->isMigrated('foo'));

        $tracker->migrate('foo', 'bar');

        self::assertTrue($tracker->isMigrated('foo'));
    }

    public function testResultsArePersisted(): void
    {
        $tracker = DocumentMigrationTracker::fromFile($this->workingPath);
        $tracker->migrate('foo', 'bar');

        $copy = DocumentMigrationTracker::fromFile($this->workingPath);
        self::assertTrue($copy->isMigrated('foo'));
        self::assertSame('bar', $copy->getTarget('foo'));
    }

    public function testYouCannotMigrateTheSameItemTwice(): void
    {
        $tracker = DocumentMigrationTracker::fromFile($this->workingPath);
        $tracker->migrate('foo', 'bar');

        $this->expectException(BadMethodCallException::class);
        $tracker->migrate('foo', 'bar');
    }

    public function testYouCannotGetTheTargetForAnItemThatHasNotBeenMigrated(): void
    {
        $tracker = DocumentMigrationTracker::fromFile($this->workingPath);
        $this->expectException(BadMethodCallException::class);
        $tracker->getTarget('foo');
    }

    public function testIteration(): void
    {
        $tracker = DocumentMigrationTracker::fromFile($this->workingPath);
        $tracker->migrate('foo', 'bar');
        $tracker->migrate('baz', 'bat');

        self::assertSame(
            ['foo' => 'bar', 'baz' => 'bat'],
            iterator_to_array($tracker),
        );
    }

    public function testSourceRegistration(): void
    {
        $tracker = DocumentMigrationTracker::fromFile($this->workingPath);
        $tracker->registerSource('foo');
        self::assertFalse($tracker->isMigrated('foo'));

        self::assertSame(['foo' => null], iterator_to_array($tracker));
    }

    public function testYouCannotRegisterASourceTwice(): void
    {
        $tracker = DocumentMigrationTracker::fromFile($this->workingPath);
        $tracker->registerSource('foo');

        $this->expectException(BadMethodCallException::class);
        $tracker->registerSource('foo');
    }

    public function testTheTrackerIsCountable(): void
    {
        $tracker = DocumentMigrationTracker::fromFile($this->workingPath);
        $tracker->registerSource('foo');
        self::assertEquals(1, $tracker->count());
        $tracker->registerSource('bar');
        self::assertEquals(2, $tracker->count());
    }
}
