<?php

declare(strict_types=1);

namespace Prismic\Cloner\Transformer;

use Override;
use Prismic\Migration\Model\Document;

use function Psl\Vec\values;

final readonly class ChainTransformer implements Transformer
{
    /** @var list<Transformer> */
    private array $transformers;

    public function __construct(Transformer ...$transformers)
    {
        $this->transformers = values($transformers);
    }

    #[Override]
    public function transform(Document $document): Document
    {
        foreach ($this->transformers as $transformer) {
            $document = $transformer->transform($document);
        }

        return $document;
    }
}
