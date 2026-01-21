<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitSelfCallRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/.php-cs-fixer.dist.php',
        __DIR__ . '/phpstan.bootstrap.php',
        __DIR__ . '/rector.php',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withImportNames(importShortClasses: false, removeUnusedImports: true)
    ->withPhpSets(php82: true)
    ->withPHPStanConfigs([
        __DIR__ . '/phpstan.neon',
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
        rectorPreset: true,
        phpunitCodeQuality: true,
    )
    ->withRules([
        PreferPHPUnitSelfCallRector::class,
    ])
    ->withSkip([
        // tests
        '*/data/*',
        '*/Fixture/*',
        '*/Fixtures/*',
        // rules
        IssetOnPropertyObjectToPropertyExistsRector::class,
        PreferPHPUnitThisCallRector::class,
    ]);
