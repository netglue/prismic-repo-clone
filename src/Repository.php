<?php

declare(strict_types=1);

namespace Prismic\Cloner;

use CuyZ\Valinor\Mapper\Source\JsonSource;
use CuyZ\Valinor\MapperBuilder;
use Override;
use Prismic\Asset\Client as AssetClient;
use Prismic\DocumentType\Client as DocumentTypeClient;
use Prismic\DocumentType\SharedSliceManagementClient;
use Prismic\Migration\DocumentClient;
use Prismic\Migration\MigrationClient;
use Prismic\Migration\Model\Document;
use Psl\File\WriteMode;
use Throwable;

use function count;
use function Psl\File\read;
use function Psl\File\write;
use function Psl\Filesystem\create_directory;
use function Psl\Filesystem\delete_directory;
use function Psl\Filesystem\read_directory;
use function Psl\Json\encode;
use function sprintf;

use const DIRECTORY_SEPARATOR;

final readonly class Repository implements RepositoryContract
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string $documentStorageDirectory
     */
    public function __construct(
        private string $name,
        private AssetClient $assetClient,
        private DocumentTypeClient&SharedSliceManagementClient $docTypeClient,
        private DocumentClient $documentClient,
        private MigrationClient $migrationClient,
        private string $documentStorageDirectory,
    ) {
    }

    #[Override]
    public function name(): string
    {
        return $this->name;
    }

    #[Override]
    public function assetClient(): AssetClient
    {
        return $this->assetClient;
    }

    #[Override]
    public function docTypeClient(): DocumentTypeClient&SharedSliceManagementClient
    {
        return $this->docTypeClient;
    }

    #[Override]
    public function documentClient(): DocumentClient
    {
        return $this->documentClient;
    }

    #[Override]
    public function migrationClient(): MigrationClient
    {
        return $this->migrationClient;
    }

    /** @inheritDoc */
    #[Override]
    public function fetchDocuments(): iterable
    {
        $files = read_directory($this->documentStorageDirectory);
        if (count($files) === 0) {
            yield from $this->downloadAllDocuments();
        }

        foreach ($files as $file) {
            yield (new MapperBuilder())
                ->allowPermissiveTypes()
                ->allowPermissiveTypes()
                ->allowSuperfluousKeys()
                ->mapper()->map(Document::class, new JsonSource(
                    read($file),
                ));
        }
    }

    /** @return list<Document> */
    private function downloadAllDocuments(): array
    {
        try {
            return $this->doDocumentDownload();
        } catch (Throwable $e) {
            delete_directory($this->documentStorageDirectory);
            create_directory($this->documentStorageDirectory);

            throw $e;
        }
    }

    /** @return list<Document> */
    private function doDocumentDownload(): array
    {
        $list = $this->documentClient->findAll();
        foreach ($list as $document) {
            $this->persistDocumentState($document);
        }

        return $list;
    }

    #[Override]
    public function persistDocumentState(Document $document): void
    {
        $path = sprintf('%s%s%s.json', $this->documentStorageDirectory, DIRECTORY_SEPARATOR, $document->id);
        write(
            $path,
            encode($document, true),
            WriteMode::Truncate,
        );
    }
}
