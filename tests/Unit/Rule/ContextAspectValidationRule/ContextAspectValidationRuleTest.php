<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\ContextAspectValidationRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use SaschaEgerer\PhpstanTypo3\Rule\ContextAspectValidationRule;
use TYPO3\CMS\Core\Context\DateTimeAspect;

/**
 * @extends RuleTestCase<ContextAspectValidationRule>
 */
final class ContextAspectValidationRuleTest extends RuleTestCase
{

	public function testRuleWithErrors(): void
	{
		$this->analyse(
			[__DIR__ . '/Fixture/UseContextApiWithUndefinedAspect.php'],
			[
				[
					'There is no aspect "foo" configured so we can\'t figure out the exact type to return when calling TYPO3\CMS\Core\Context\Context::getAspect',
					13,
					'You should add custom aspects to the typo3.contextApiGetAspectMapping setting.',
				],
				[
					'There is no aspect "dates" configured so we can\'t figure out the exact type to return when calling TYPO3\CMS\Core\Context\Context::getPropertyFromAspect',
					16,
					'You should add custom aspects to the typo3.contextApiGetAspectMapping setting.',
				],
			]
		);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [__DIR__ . '/../../../../extension.neon'];
	}

	public function testRuleWithoutErrors(): void
	{
		$this->analyse([__DIR__ . '/Fixture/UseContextApiWithDefinedAspect.php'], []);
	}

	protected function getRule(): Rule
	{
		return new ContextAspectValidationRule([
			'date' => DateTimeAspect::class,
		]);
	}

}
