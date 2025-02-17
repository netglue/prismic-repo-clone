<?php

declare(strict_types=1);

namespace Prismic\Cloner\Migration;

use Prismic\Migration\Model\Document;

use function Psl\Vec\values;

/** @psalm-api */
final readonly class TitleResolverChain implements TitleResolver
{
    /** @var list<TitleResolver> */
    private array $resolvers;

    public function __construct(TitleResolver ...$resolvers)
    {
        $this->resolvers = values($resolvers);
    }

    public function resolve(Document $document): string|null
    {
        foreach ($this->resolvers as $resolver) {
            $title = $resolver->resolve($document);
            if ($title !== null) {
                return $title;
            }
        }

        return null;
    }
}
