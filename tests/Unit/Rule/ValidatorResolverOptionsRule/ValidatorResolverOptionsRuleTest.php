<?php
declare(strict_types=1);


namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\ValidatorResolverOptionsRule;


use PHPStan\Testing\RuleTestCase;
use SaschaEgerer\PhpstanTypo3\Rule\ValidatorResolverOptionsRule;

/**
 * @extends RuleTestCase<ValidatorResolverOptionsRule>
 */
final class ValidatorResolverOptionsRuleTest extends RuleTestCase
{
	/**
	 * @dataProvider provideDataWithErrors()
	 * @param list<array{0: string, 1: int, 2?: string}> $expectedErrorMessagesWithLines
	 */
	public function testRuleWithErrors(string $filePath, array $expectedErrorMessagesWithLines): void
	{
		$this->analyse([$filePath], $expectedErrorMessagesWithLines);
	}

	public function provideDataWithErrors(): \Iterator
	{
		yield [
			__DIR__ . '/Fixture/CreateValidatorWithMissingRequiredOption.php',
			[
				[
					'Required validation option not set: regularExpression', 15
				],
				[
					'Required validation option not set: regularExpression', 19
				]
			],
		];

		yield [
			__DIR__ . '/Fixture/CreateValidatorWithNonExistingOption.php',
			[
				[
					'Unsupported validation option(s) found: non-existing-option', 16
				],
				[
					'Unsupported validation option(s) found: foo', 24
				]
			],
		];
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [__DIR__ . '/../../../../extension.neon'];
	}

	protected function getRule(): \PHPStan\Rules\Rule
	{
		return self::getContainer()->getByType(ValidatorResolverOptionsRule::class);
	}
}
