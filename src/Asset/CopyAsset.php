<?php

declare(strict_types=1);

namespace Prismic\Cloner\Asset;

use Fig\Http\Message\RequestMethodInterface;
use League\MimeTypeDetection\MimeTypeDetector;
use Prismic\Asset\Client;
use Prismic\Asset\Model\Asset;
use Prismic\Asset\Model\AssetTag;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use RuntimeException;
use Throwable;

use function array_find;
use function array_map;
use function basename;
use function is_string;
use function parse_url;
use function pathinfo;
use function Psl\Type\non_empty_string;
use function Psl\Type\null;
use function Psl\Type\union;
use function sprintf;
use function str_ends_with;

use const PATHINFO_EXTENSION;
use const PHP_URL_PATH;

/** @psalm-api */
final readonly class CopyAsset
{
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
        private UriFactoryInterface $uriFactory,
        private MimeTypeDetector $mimeTypeDetector,
    ) {
    }

    public function copy(Asset $asset, Client $uploadClient): Asset
    {
        $fileName = $this->resolveFileName($asset);
        $fileContent = $this->fetchFileContent($asset);
        $mimeType = $this->mimeTypeDetector->detectMimeType($fileName, $fileContent);
        if (! is_string($mimeType) || $mimeType === '') {
            throw new RuntimeException(sprintf(
                'Failed to detect mime type for the asset %s',
                $asset->url,
            ));
        }

        $tags = array_map(static fn (AssetTag $tag): string => $tag->name, $asset->tags);

        return $uploadClient->uploadAsset(
            $fileContent,
            $fileName,
            $mimeType,
            $asset->notes === '' ? null : $asset->notes,
            $asset->credits === '' ? null : $asset->credits,
            $asset->alt === '' ? null : $asset->alt,
            $tags,
        );
    }

    /** @return non-empty-string */
    private function resolveFileName(Asset $asset): string
    {
        $basename = non_empty_string()->assert(basename(
            non_empty_string()->assert(
                parse_url($asset->url, PHP_URL_PATH),
            ),
        ));

        $possible = [
            $this->extension($basename),
            $this->extension($asset->filename),
            $asset->extension,
        ];

        $extension = union(non_empty_string(), null())->assert(
            array_find($possible, static fn (string|null $ext): bool => is_string($ext)),
        );

        if ($extension === null) {
            throw new RuntimeException('Cannot determine a filename extension from available information');
        }

        $filename = $asset->filename === '' ? $basename : $asset->filename;
        if (str_ends_with($filename, $extension)) {
            return $filename;
        }

        return $basename;
    }

    /** @return non-empty-string|null */
    private function extension(string $filename): string|null
    {
        if ($filename === '') {
            return null;
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        return $extension !== '' ? $extension : null;
    }

    /** @return non-empty-string */
    private function fetchFileContent(Asset $asset): string
    {
        // Strip query so we are downloading the original asset, un-compressed etc.
        $uri = $this->uriFactory->createUri($asset->url)->withQuery('');

        try {
            $response = $this->httpClient->sendRequest(
                $this->requestFactory->createRequest(
                    RequestMethodInterface::METHOD_GET,
                    $uri,
                ),
            );

            return non_empty_string()->assert((string) $response->getBody());
        } catch (Throwable $e) {
            throw new RuntimeException(sprintf(
                'Failed to download asset file contents for %s',
                (string) $uri,
            ), (int) $e->getCode(), $e);
        }
    }
}
