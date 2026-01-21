<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\ValidatorResolverOptionsRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use SaschaEgerer\PhpstanTypo3\Rule\ValidatorResolverOptionsRule;

/**
 * @extends RuleTestCase<ValidatorResolverOptionsRule>
 */
final class ValidatorResolverOptionsRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideDataWithErrors
     *
     * @param list<array{0: string, 1: int, 2?: string}> $expectedErrorMessagesWithLines
     */
    public function testRuleWithErrors(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function testRuleWithoutErrors(): void
    {
        $this->analyse([__DIR__ . '/Fixture/CreateValidatorWithCorrectOptions.php'], []);
    }

    public static function provideDataWithErrors(): \Iterator
    {
        yield [
            __DIR__ . '/Fixture/CreateValidatorWithUnresolvableType.php',
            [
                [
                    'Could not create validator for "Foo"',
                    14,
                ],
            ],
        ];

        yield [
            __DIR__ . '/Fixture/CreateValidatorWithMissingRequiredOption.php',
            [
                [
                    'Required validation option not set: regularExpression',
                    15,
                ],
                [
                    'Required validation option not set: regularExpression',
                    20,
                ],
            ],
        ];

        yield [
            __DIR__ . '/Fixture/CreateValidatorWithNonExistingOption.php',
            [
                [
                    'Unsupported validation option(s) found: non-existing-option',
                    15,
                ],
                [
                    'Unsupported validation option(s) found: foo',
                    23,
                ],
                [
                    'Unsupported validation option(s) found: minmum',
                    27,
                ],
            ],
        ];
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/../../../../extension.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(ValidatorResolverOptionsRule::class);
    }

}
