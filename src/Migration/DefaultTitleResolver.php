<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration;

use Primo\Cli\TypeBuilder;
use Prismic\Cloner\DocumentType\DocumentTypes;
use Prismic\DocumentType\Definition;
use Prismic\Migration\Model\Document;

use function array_key_exists;
use function in_array;
use function is_array;
use function is_string;
use function Psl\Json\typed;
use function Psl\Type\mixed_dict;
use function reset;

/**
 * Resolves a title for the given document if possible
 *
 * Only looks at top-level document fragments where `useAsTitle` is `true` in the corresponding document type. The
 * resolved fragment must also be either a Rich Text or Plain Text fragment.
 *
 * @psalm-api
 */
final readonly class DefaultTitleResolver implements TitleResolver
{
    public function __construct(
        private DocumentTypes $types,
    ) {
    }

    /** @return non-empty-string|null */
    public function resolve(Document $document): string|null
    {
        $type = $this->types->get($document->type);
        $path = $this->resolveFragmentPathForType($type);
        if ($path === null) {
            return null;
        }

        return $this->extractTitleFromPath($path, $document);
    }

    /** @return non-empty-string|null */
    private function extractTitleFromPath(string $path, Document $document): string|null
    {
        /** @psalm-var mixed $fragment */
        $fragment = $document->data[$path] ?? null;
        if ($fragment === null) {
            return null;
        }

        // Plain text fields are stand-alone strings
        if (is_string($fragment) && $fragment !== '') {
            return $fragment;
        }

        // Rich-text fields are lists of objects
        if (is_array($fragment)) {
            $first = reset($fragment);
            if (! is_array($first)) {
                return null;
            }

            if (isset($first['text']) && is_string($first['text']) && $first['text'] !== '') {
                return $first['text'];
            }
        }

        return null;
    }

    /** @return non-empty-string|null */
    private function resolveFragmentPathForType(Definition $definition): string|null
    {
        $def = typed($definition->json(), mixed_dict());

        /** @psalm-var mixed $section */
        foreach ($def as $section) {
            if (! is_array($section)) {
                continue;
            }

            /** @psalm-var mixed $data */
            foreach ($section as $name => $data) {
                if (! is_string($name) || $name === '' || ! is_array($data)) {
                    continue;
                }

                $useAsTitle = $this->searchForUseAsTitle($name, $data);
                if ($useAsTitle === null) {
                    continue;
                }

                return $useAsTitle;
            }
        }

        return null;
    }

    /**
     * Search a document fragment to see if it has been configured for use as the title
     *
     * @param non-empty-string        $path
     * @param array<array-key, mixed> $data
     *
     * @return non-empty-string|null
     */
    private function searchForUseAsTitle(string $path, array $data): string|null
    {
        $searchTypes = [
            TypeBuilder::TYPE_RICH,
            TypeBuilder::TYPE_TEXT,
        ];

        $type = $data['type'] ?? null;
        if (! is_string($type) || ! in_array($type, $searchTypes, true)) {
            return null;
        }

        $config = $data['config'] ?? [];
        if (! is_array($config)) {
            return null;
        }

        if (array_key_exists('useAsTitle', $config) && $config['useAsTitle'] === true) {
            return $path;
        }

        return null;
    }
}
