<?php

declare(strict_types=1);

// phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly

use Prismic\Cloner\Factory\RepositoryFactory;

return [
    'repositories' => [
        $_ENV['SOURCE_REPO'] => [
            'name' => $_ENV['SOURCE_REPO'],
            'writeToken' => $_ENV['SOURCE_WRITE_TOKEN'],
        ],
        $_ENV['TARGET_REPO'] => [
            'name' => $_ENV['TARGET_REPO'],
            'writeToken' => $_ENV['TARGET_WRITE_TOKEN'],
        ],
    ],

    'dependencies' => [
        'factories' => [
            Prismic\Cloner\SourceRepository::class => [RepositoryFactory::class, $_ENV['SOURCE_REPO']],
            Prismic\Cloner\TargetRepository::class => [RepositoryFactory::class, $_ENV['TARGET_REPO']],
        ],
    ],
];
