<?php

declare(strict_types=1);

/**
 * This file is included by PHPStan NEON configuration to provide dynamic paths.
 * PHPStan resolves %rootDir% and %currentWorkingDirectory% relative to the phar,
 * so we need to provide absolute paths from PHP.
 */
$systemSettingsPath = dirname(__DIR__) . '/Fixtures/settings.php';

return [
    'parameters' => [
        'typo3' => [
            'systemSettingsPath' => $systemSettingsPath,
        ],
    ],
];
