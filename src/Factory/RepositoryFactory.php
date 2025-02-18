<?php

declare(strict_types=1);

namespace Prismic\Cloner\Factory;

use Prismic\Cloner\Asset\AssetClientFactory;
use Prismic\Cloner\DocumentType\DocumentTypeClientFactory;
use Prismic\Cloner\Migration\Factory\DocumentClientFactory;
use Prismic\Cloner\Migration\Factory\MigrationClientFactory;
use Prismic\Cloner\PathConfig;
use Prismic\Cloner\Repository;
use Prismic\Cloner\RepositoryContract;
use Psr\Container\ContainerInterface;

use function Psl\Type\instance_of;

final readonly class RepositoryFactory
{
    /** @param non-empty-string $name */
    public function __construct(private string $name)
    {
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed>      $arguments
     *
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function __callStatic(string $name, array $arguments): RepositoryContract
    {
        return (new self($name))(
            instance_of(ContainerInterface::class)->assert(
                $arguments[0] ?? null,
            ),
        );
    }

    public function __invoke(ContainerInterface $container): RepositoryContract
    {
        $paths = $container->get(PathConfig::class);

        return new Repository(
            $this->name,
            AssetClientFactory::create($container, $this->name),
            DocumentTypeClientFactory::create($container, $this->name),
            DocumentClientFactory::create($container, $this->name),
            MigrationClientFactory::create($container, $this->name),
            $paths->repositoryDocumentCache($this->name),
        );
    }
}
