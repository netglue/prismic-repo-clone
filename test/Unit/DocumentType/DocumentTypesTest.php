<?php

declare(strict_types=1);

namespace App\Test\Unit\DocumentType;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Prismic\Cloner\DocumentType\DocumentTypes;
use Prismic\DocumentType\Definition;

use function iterator_to_array;
use function Psl\Filesystem\delete_file;
use function Psl\Filesystem\exists;
use function Psl\Json\encode;

final class DocumentTypesTest extends TestCase
{
    /** @var non-empty-string */
    private string $workingPath;

    protected function setUp(): void
    {
        $this->workingPath = __DIR__ . '/DocumentTypeFixtures/working.json';
    }

    protected function tearDown(): void
    {
        if (! exists($this->workingPath)) {
            return;
        }

        delete_file($this->workingPath);
    }

    public function testFromTypes(): void
    {
        $list = [
            Definition::new(
                'some-type',
                'Some Label',
                true,
                true,
                '{}',
            ),
        ];

        $types = DocumentTypes::fromTypes($list);

        self::assertCount(1, $types);
        self::assertSame($list, iterator_to_array($types));
    }

    public function testFromFile(): void
    {
        $types = DocumentTypes::fromFile(__DIR__ . '/DocumentTypeFixtures/example.json');

        self::assertCount(2, $types);

        $type = $types->get('some-type');
        self::assertSame('Some Label', $type->label());
    }

    public function testGetIsExceptionalWhenNotFound(): void
    {
        $types = DocumentTypes::fromFile(__DIR__ . '/DocumentTypeFixtures/example.json');

        $this->expectException(InvalidArgumentException::class);

        $types->get('foo');
    }

    public function testListCanBeWrittenToAFile(): void
    {
        $types = DocumentTypes::fromFile(__DIR__ . '/DocumentTypeFixtures/example.json');
        $types->freezeTo($this->workingPath);

        $clone = DocumentTypes::fromFile($this->workingPath);

        self::assertSame(
            encode($types),
            encode($clone),
        );
    }
}
