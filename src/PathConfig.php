<?php

declare(strict_types=1);

namespace Prismic\Cloner;

use function Psl\Filesystem\create_directory;
use function sprintf;

use const DIRECTORY_SEPARATOR;

final readonly class PathConfig
{
    /**
     * @param non-empty-string $dataDirectory
     * @param non-empty-string $assetMapFilename
     * @param non-empty-string $assetListFilename
     * @param non-empty-string $typeDefProgressFilename
     * @param non-empty-string $typeDefinitionsFilename
     */
    public function __construct(
        public string $dataDirectory,
        public string $assetMapFilename,
        public string $assetListFilename,
        public string $typeDefProgressFilename,
        public string $typeDefinitionsFilename,
    ) {
    }

    public function createDirectories(): void
    {
        create_directory($this->dataDirectory);
    }

    /** @return non-empty-string */
    public function assetListPath(): string
    {
        return sprintf(
            '%s%s%s',
            $this->dataDirectory,
            DIRECTORY_SEPARATOR,
            $this->assetListFilename,
        );
    }

    /** @return non-empty-string */
    public function assetMapPath(): string
    {
        return sprintf(
            '%s%s%s',
            $this->dataDirectory,
            DIRECTORY_SEPARATOR,
            $this->assetMapFilename,
        );
    }

    /** @return non-empty-string */
    public function typeDefProgressPath(): string
    {
        return sprintf(
            '%s%s%s',
            $this->dataDirectory,
            DIRECTORY_SEPARATOR,
            $this->typeDefProgressFilename,
        );
    }

    /** @return non-empty-string */
    public function typeDefinitionsPath(): string
    {
        return sprintf(
            '%s%s%s',
            $this->dataDirectory,
            DIRECTORY_SEPARATOR,
            $this->typeDefinitionsFilename,
        );
    }
}
