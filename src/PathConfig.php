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
     * @param non-empty-string $documentMigrationTrackerFilename
     * @param non-empty-string $documentCacheDirectoryName
     * @param non-empty-string $patchTrackerFileName
     * @param non-empty-string $assetUsageFileName
     */
    public function __construct(
        public string $dataDirectory,
        public string $assetMapFilename,
        public string $assetListFilename,
        public string $typeDefProgressFilename,
        public string $typeDefinitionsFilename,
        public string $documentMigrationTrackerFilename,
        public string $documentCacheDirectoryName,
        public string $patchTrackerFileName,
        public string $assetUsageFileName,
    ) {
    }

    public function createDirectories(): void
    {
        create_directory($this->dataDirectory);
        create_directory($this->documentCacheDirectory());
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

    /** @return non-empty-string */
    public function documentMigrationTrackerPath(): string
    {
        return sprintf(
            '%s%s%s',
            $this->dataDirectory,
            DIRECTORY_SEPARATOR,
            $this->documentMigrationTrackerFilename,
        );
    }

    /** @return non-empty-string */
    public function patchTrackerFilePath(): string
    {
        return sprintf(
            '%s%s%s',
            $this->dataDirectory,
            DIRECTORY_SEPARATOR,
            $this->patchTrackerFileName,
        );
    }

    /** @return non-empty-string */
    public function documentCacheDirectory(): string
    {
        return sprintf(
            '%s%s%s',
            $this->dataDirectory,
            DIRECTORY_SEPARATOR,
            $this->documentCacheDirectoryName,
        );
    }

    /** @return non-empty-string */
    public function assetUsageFilePath(): string
    {
        return sprintf(
            '%s%s%s',
            $this->dataDirectory,
            DIRECTORY_SEPARATOR,
            $this->assetUsageFileName,
        );
    }

    /**
     * @param non-empty-string $name
     *
     * @return non-empty-string
     */
    public function repositoryDocumentCache(string $name): string
    {
        $directory = sprintf(
            '%s%s%s',
            $this->documentCacheDirectory(),
            DIRECTORY_SEPARATOR,
            $name,
        );

        create_directory($directory);

        return $directory;
    }
}
