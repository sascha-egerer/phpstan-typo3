<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\ValidatorResolverOptionsRule\Fixture;

use TYPO3\CMS\Extbase\Validation\Validator\RegularExpressionValidator;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;

final class CreateValidatorWithNonExistingOption
{

	private const OPTION_MINIMUM = 'minmum';

	public function __construct()
	{
		$validatorResolver = new ValidatorResolver();
		$validatorResolver->createValidator(
			RegularExpressionValidator::class,
			[
				'regularExpression' => 'foo',
				'non-existing-option' => 'bar',
			]
		);

		$validatorResolver->createValidator('StringLength', [
			'foo' => 'bar',
		]);

		$validatorName = 'NumberRange';
		$validatorResolver->createValidator($validatorName, [
			self::OPTION_MINIMUM => 1,
		]);
	}

}
