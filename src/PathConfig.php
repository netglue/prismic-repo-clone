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
     */
    public function __construct(
        public string $dataDirectory,
        public string $assetMapFilename,
        public string $assetListFilename,
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
}
