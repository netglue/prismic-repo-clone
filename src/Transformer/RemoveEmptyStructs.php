<?php

declare(strict_types=1);

namespace Prismic\Cloner\Transformer;

use Override;
use Prismic\Migration\Model\Document;

use function array_all;
use function array_key_exists;
use function array_keys;
use function in_array;
use function is_array;
use function is_string;

final readonly class RemoveEmptyStructs implements Transformer
{
    #[Override]
    public function transform(Document $document): Document
    {
        /**
         * Keys are preserved, so ignore rather than assert
         *
         * @psalm-suppress MixedArgumentTypeCoercion
         */
        return new Document(
            $document->id,
            $document->uid,
            $document->type,
            $document->lang,
            $document->tags,
            $this->processDocumentData($document->data), // @phpstan-ignore argument.type
        );
    }

    /**
     * @param array<array-key, mixed> $data
     *
     * @return array<array-key, mixed>
     */
    private function processDocumentData(array $data): array
    {
        // These keys should always be left as an empty list when found empty
        $keysToSkipForEmptyCheck = [
            'items',
            'tags',
            'spans',
        ];

        foreach ($data as $key => $datum) {
            if (! is_array($datum)) {
                continue;
            }

            if (in_array($key, $keysToSkipForEmptyCheck, true) && $datum === []) {
                continue;
            }

            if ($this->isSlice($datum)) {
                $data[$key] = $this->processSlice($datum);

                continue;
            }

            // Empty images can be removed, as can other empty lists, such as rich text fields
            if ($this->isEmptyImage($datum) || $datum === []) {
                unset($data[$key]);

                continue;
            }

            $data[$key] = $this->processDocumentData($datum);
        }

        return $data;
    }

    /** @param array<array-key, mixed> $data */
    private function isSlice(array $data): bool
    {
        return array_key_exists('slice_type', $data);
    }

    /**
     * @param array<array-key, mixed> $data
     *
     * @return array<array-key, mixed>
     */
    private function processSlice(array $data): array
    {
        // An empty 'primary' must be an empty object, not []
        if (is_array($data['primary'])) {
            $data['primary'] = $this->processDocumentData($data['primary']);
            if ($data['primary'] === []) {
                $data['primary'] = (object) [];
            }
        }

        if (is_array($data['items'])) {
            $data['items'] = $this->processDocumentData($data['items']);
        }

        return $data;
    }

    /** @param array<array-key, mixed> $data */
    private function isEmptyImage(array $data): bool
    {
        if ($data === []) {
            return false;
        }

        $isMap = array_all(array_keys($data), static fn (int|string $key): bool => is_string($key));
        $allEmpty = array_all($data, static fn (mixed $value): bool => $value === []);

        return $isMap && $allEmpty;
    }
}
