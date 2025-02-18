<?php

declare(strict_types=1);

return [
    /**
     * Asset tagging
     *
     * Asset tags are a relatively new feature, so like me, you may have used notes for writing search keywords to
     * make it easier to find images in large libraries.
     *
     * During asset migration, you can scan existing notes and/or alt text for used words and map those words to
     * one or more tags.
     *
     * Any existing asset tags are preserved, but I'm pretty sure that the tags are case-sensitive (in Prismic).
     *
     * Note that you must explicitly enable the feature by setting the `searchAsset(AltText|Notes)ForTags` to true
     * as well as setting up a dict to match source text to a list of desired tags.
     *
     * Note that the search value (key) is case-insensitive
     *
     * The array is `'search for' => ['Tag 1', 'Tag 2']`, for example
     * ```
     * [
     *     'car' => ['Car', 'Transport'],
     *     'automobile' => ['Car', 'Transport'],
     *     'train' => ['Train', 'Transport'],
     *     'choo-choo' => ['Train', 'Transport'],
     *     'plane' => ['Aeroplane', 'Transport'],
     *     'aeroplane' => ['Aeroplane', 'Transport'],
     *     'jet plane' => ['Aeroplane', 'Transport'],
     * ],
     */
    'assetTagMap' => [],
    'searchAssetAltTextForTags' => false,
    'searchAssetNotesForTags' => false,
    'searchAssetTitlesForTags' => false,
];
