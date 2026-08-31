<?php declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // include *.php files in the root directory
    ->withRootFiles()

    // add a single rule
    ->withRules([
        NoUnusedImportsFixer::class,
    ])

    // add sets - group of rules, from easiest to more complex ones
    // uncomment one, apply one, commit, PR, merge and repeat
    //->withPreparedSets(
    //      spaces: true,
    //      namespaces: true,
    //      docblocks: true,
    //      arrays: true,
    //      comments: true,
    //)

    // ...but first: take it step by step
    ->withSpacesLevel(2)
    ->withArrayLevel(2)
    ->withControlStructuresLevel(2)
    ->withDocblockLevel(2);
