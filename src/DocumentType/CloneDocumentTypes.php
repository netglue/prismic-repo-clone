<?php

declare(strict_types=1);

namespace Prismic\Cloner\DocumentType;

use Prismic\Cloner\RepositoryContract;
use Prismic\DocumentType\Definition;
use Prismic\DocumentType\Exception\InvalidDefinition;
use Prismic\DocumentType\SharedSlice;
use Psl\File\WriteMode;
use RuntimeException;

use function in_array;
use function json_validate;
use function Psl\File\read;
use function Psl\File\write;
use function Psl\Filesystem\exists;
use function Psl\Json\decode;
use function Psl\Json\encode;
use function Psl\Json\typed;
use function Psl\Type\non_empty_string;
use function Psl\Type\shape;
use function Psl\Type\vec;
use function Psl\Vec\map;
use function Psl\Vec\values;
use function sprintf;

final class CloneDocumentTypes
{
    /** @var list<non-empty-string> */
    private array $typesToCopy = [];

    /** @var list<non-empty-string> */
    private array $slicesToCopy = [];

    /** @var list<non-empty-string> */
    private array $copiedTypes = [];

    /** @var list<non-empty-string> */
    private array $copiedSlices = [];

    /** @param non-empty-string $progressFilePath */
    public function __construct(
        private readonly RepositoryContract $source,
        private readonly RepositoryContract $target,
        private readonly DocumentTypes $documentTypes,
        private readonly string $progressFilePath,
    ) {
    }

    public function __invoke(): void
    {
        $this->initialize();
        $this->copyDocumentTypes();
        $this->copySharedSlices();
    }

    private function initialize(): void
    {
        if (! exists($this->progressFilePath)) {
            $this->typesToCopy = values(map(
                $this->documentTypes,
                static fn (Definition $definition): string => $definition->id(),
            ));
            $slices = $this->source->docTypeClient()->fetchAllSharedSlices();
            $this->slicesToCopy = values(map($slices, static fn (SharedSlice $slice): string => $slice->id));

            $this->freeze();

            return;
        }

        $this->unfreeze();
    }

    private function copyDocumentTypes(): void
    {
        $all = $this->source->docTypeClient()->fetchAllDefinitions();

        foreach ($all as $def) {
            if (in_array($def->id(), $this->copiedTypes, true)) {
                continue;
            }

            try {
                $this->target->docTypeClient()->saveDefinition($def);
            } catch (InvalidDefinition $error) {
                $body = (string) $error->response()->getBody();
                if (json_validate($body)) {
                    $body = encode(decode($body), true);
                }

                throw new RuntimeException(sprintf(
                    'Failed to clone document type "%s". Response body error was: %s',
                    $def->id(),
                    $body,
                ));
            }

            $this->copiedTypes[] = $def->id();
            $this->freeze();
        }
    }

    private function copySharedSlices(): void
    {
        $all = $this->source->docTypeClient()->fetchAllSharedSlices();

        foreach ($all as $slice) {
            if (in_array($slice->id, $this->copiedSlices, true)) {
                continue;
            }

            $this->target->docTypeClient()->saveSharedSlice($slice);
            $this->copiedSlices[] = $slice->id;
            $this->freeze();
        }
    }

    private function unfreeze(): void
    {
        $payload = typed(
            read($this->progressFilePath),
            shape([
                'typesToCopy' => vec(non_empty_string()),
                'slicesToCopy' => vec(non_empty_string()),
                'copiedTypes' => vec(non_empty_string()),
                'copiedSlices' => vec(non_empty_string()),
            ]),
        );

        $this->typesToCopy = $payload['typesToCopy'];
        $this->slicesToCopy = $payload['slicesToCopy'];
        $this->copiedTypes = $payload['copiedTypes'];
        $this->copiedSlices = $payload['copiedSlices'];
    }

    private function freeze(): void
    {
        write(
            $this->progressFilePath,
            encode([
                'typesToCopy' => $this->typesToCopy,
                'slicesToCopy' => $this->slicesToCopy,
                'copiedTypes' => $this->copiedTypes,
                'copiedSlices' => $this->copiedSlices,
            ], true),
            WriteMode::Truncate,
        );
    }
}
