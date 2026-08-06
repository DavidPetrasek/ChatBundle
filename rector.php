<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddTypeFromResourceDocblockRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets(php82: true)
    ->withSets([
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
    ])
    ->withComposerBased(symfony: true)
    // ->withRules([])
    ->withPreparedSets
    (
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        doctrineCodeQuality: true,
        symfonyCodeQuality: true,
        symfonyConfigs: true,
    )
    ->withAttributesSets(symfony: true, doctrine: true)
;


// USE THIS FOR DOWNGRADE
// use Rector\Set\ValueObject\DowngradeLevelSetList;
// use Rector\Config\RectorConfig;

// return static function (RectorConfig $rectorConfig): void {
//     $rectorConfig->sets([
//         DowngradeLevelSetList::DOWN_TO_PHP_82
//     ]);
//     $rectorConfig->paths([
//         __DIR__ . '/src',
//     ]);
// };