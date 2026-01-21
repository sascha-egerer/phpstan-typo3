<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\GeneralUtilityMakeInstancePrivateServiceRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use SaschaEgerer\PhpstanTypo3\Rule\GeneralUtilityMakeInstancePrivateServiceRule;

/**
 * @extends RuleTestCase<GeneralUtilityMakeInstancePrivateServiceRule>
 */
final class GeneralUtilityMakeInstancePrivateServiceRuleTest extends RuleTestCase
{
    public function testGetPrivateServiceWith(): void
    {
        $this->analyse(
            [
                __DIR__ . '/Fixture/ExampleController.php',
            ],
            [
                [
                    'Service "private" is private.',
                    13,
                ],
            ]
        );
    }

    public function testGetPublicService(): void
    {
        $this->analyse(
            [
                __DIR__ . '/Fixture/ExampleController2.php',
            ],
            []
        );
    }

    public function testGetNonExistingService(): void
    {
        $this->analyse(
            [
                __DIR__ . '/Fixture/ExampleController3.php',
            ],
            []
        );
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
        return self::getContainer()->getByType(GeneralUtilityMakeInstancePrivateServiceRule::class);
    }

}
