<?php declare(strict_types=1);

use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\Config\RectorConfig;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Set\ValueObject\SetList;
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
    ->withSets
    ([
        SetList::DEAD_CODE, 
        SetList::CODE_QUALITY, 
        SetList::CODING_STYLE, 
        SetList::TYPE_DECLARATION
    ])
    ->withAttributesSets(symfony: true, doctrine: true)
    ->withSkip([
        NewlineAfterStatementRector::class,
        NewlineBetweenClassLikeStmtsRector::class
    ]);
;