<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\AllowedPhpDisableFunctionsUsageExtension;

use PHPStan\Rules\RestrictedUsage\RestrictedFunctionUsageRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RestrictedFunctionUsageRule>
 */
final class AllowedPhpDisableFunctionsUsageExtensionTest extends RuleTestCase
{
    public function testRuleWithErrors(): void
    {
        $this->analyse(
            [__DIR__ . '/Fixture/UseAllowedPhpDisableFunction.php'],
            [
                [
                    'Function set_time_limit() is listed in TYPO3_CONF_VARS/SYS/allowedPhpDisableFunctions and might be disabled via the PHP setting "disable_functions" at runtime.',
                    11,
                ],
                [
                    'Function putenv() is listed in TYPO3_CONF_VARS/SYS/allowedPhpDisableFunctions and might be disabled via the PHP setting "disable_functions" at runtime.',
                    12,
                ],
                [
                    'Function phpinfo() is listed in TYPO3_CONF_VARS/SYS/allowedPhpDisableFunctions and might be disabled via the PHP setting "disable_functions" at runtime.',
                    13,
                ],
            ]
        );
    }

    public function testRuleWithoutErrors(): void
    {
        $this->analyse([__DIR__ . '/Fixture/UseNotDisabledPhpFunction.php'], []);
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/../../../../extension.neon',
            __DIR__ . '/configuration.neon',
        ];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(RestrictedFunctionUsageRule::class);
    }

}
