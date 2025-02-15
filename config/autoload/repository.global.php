<?php

declare(strict_types=1);

// phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly

use Prismic\Cloner\Factory\RepositoryFactory;

return [
    /**
     * Stupid API Keys required for the migration API found at:
     * https://prismic.io/docs/migration-api-technical-reference#limits
     */
    'migrationApiKeys' => [
        'cSaZlfkQlF9C6CEAM2Del6MNX9WonlV86HPbeEJL',
        'pZCexCajUQ4jriYwIGSxA1drZrFxDyFf1S0D1K0P',
        'Yc0mfrkGDw8gaaGKTrzwC3QUZDajv6k73DA99vWN',
        'ySzSEbVMAb5S1oSCQfbVG4mbh9Cb8wlF7BCvKI0L',
        'g2DA3EKWvx8uxVYcNFrmT5nJpon1Vi9V4XcOibJD',
        'CCNIlI0Vz41J66oFwsHUXaZa6NYFIY6z7aDF62Bc',
    ],

    'repositories' => [
        $_ENV['SOURCE_REPO'] => [
            'name' => $_ENV['SOURCE_REPO'],
            'writeToken' => $_ENV['SOURCE_WRITE_TOKEN'],
            'readToken' => $_ENV['SOURCE_READ_TOKEN'],
        ],
        $_ENV['TARGET_REPO'] => [
            'name' => $_ENV['TARGET_REPO'],
            'writeToken' => $_ENV['TARGET_WRITE_TOKEN'],
            'readToken' => $_ENV['TARGET_READ_TOKEN'],
        ],
    ],

    'dependencies' => [
        'factories' => [
            Prismic\Cloner\SourceRepository::class => [RepositoryFactory::class, $_ENV['SOURCE_REPO']],
            Prismic\Cloner\TargetRepository::class => [RepositoryFactory::class, $_ENV['TARGET_REPO']],
        ],
    ],
];
