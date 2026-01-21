<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\ContainerInterfacePrivateServiceRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use SaschaEgerer\PhpstanTypo3\Rule\ContainerInterfacePrivateServiceRule;

/**
 * @extends RuleTestCase<ContainerInterfacePrivateServiceRule>
 */
final class ContainerInterfacePrivateServiceRuleTest extends RuleTestCase
{
    public function testGetPrivateService(): void
    {
        $this->analyse(
            [
                __DIR__ . '/Fixture/ExampleController.php',
            ],
            [
                [
                    'Service "private" is private.',
                    19,
                ],
            ]
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
        return self::getContainer()->getByType(ContainerInterfacePrivateServiceRule::class);
    }

}
