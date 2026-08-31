<?php declare(strict_types=1);

use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\Config\RectorConfig;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Symfony\CodeQuality\Rector\Class_\InlineClassRoutePrefixRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets(php82: true)
    ->withComposerBased(symfony: true)
    ->withRules([
        DeclareStrictTypesRector::class
    ])
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
    ->withSkip([
        InlineClassRoutePrefixRector::class,
        NewlineAfterStatementRector::class,
        NewlineBetweenClassLikeStmtsRector::class
    ]);
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