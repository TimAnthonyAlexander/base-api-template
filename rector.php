<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/routes',
    ])
    ->withSkip([
        __DIR__ . '/vendor',
        __DIR__ . '/storage',
    ])
    ->withSets([
        // Core language modernization to PHP 8.4
        LevelSetList::UP_TO_PHP_84,

        // Aggressive quality improvements for modern application code
        SetList::CODE_QUALITY,
        SetList::TYPE_DECLARATION,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,

        // Coding standards and imports
        SetList::CODING_STYLE,

        // More opinionated improvements for application code
        SetList::PRIVATIZATION,
        SetList::NAMING,
        SetList::INSTANCEOF,
    ])
    ->withImportNames(
        importShortClasses: true,
        removeUnusedImports: true
    )
    ->withParallel()
    ->withCache(__DIR__ . '/var/cache/rector')
    ->withPhpSets(php84: true);
