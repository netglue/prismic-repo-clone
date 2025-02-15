<?php

declare(strict_types=1);

use function Psl\Type\non_empty_string;

return [
    'app' => [
        // The directory where we store data files
        'data-directory' => sprintf(
            '%s/../../var/data/%s-to-%s',
            __DIR__,
            non_empty_string()->assert($_ENV['SOURCE_REPO']),
            non_empty_string()->assert($_ENV['TARGET_REPO']),
        ),

        // The file in which we record which source asset maps to which target asset
        'asset-map-filename' => 'asset-map.json',

        // The file where we save the list of source assets
        'asset-list-filename' => 'asset-list.json',

        // The file where we record progress on duplication of document types and slices
        'type-definition-progress-filename' => 'doc-type-progress.json',

        // A file where all type definitions are stored
        'type-definitions-filename' => 'type-definitions.json',

        // Where a map of migrated document id's is persisted
        'doc-migration-tracker-filename' => 'migrated-documents.json',
    ],
];
