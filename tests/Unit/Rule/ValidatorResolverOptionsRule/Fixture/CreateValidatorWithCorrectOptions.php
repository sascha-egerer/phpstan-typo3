<?php
declare(strict_types=1);


namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\ValidatorResolverOptionsRule\Fixture;


use TYPO3\CMS\Extbase\Validation\Validator\NumberRangeValidator;
use TYPO3\CMS\Extbase\Validation\Validator\RegularExpressionValidator;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;

final class CreateValidatorWithCorrectOptions
{
	public function __construct()
	{
		$validatorResolver = new ValidatorResolver();
		$validatorResolver->createValidator(
			RegularExpressionValidator::class, [
				RegularExpressionValidator::REGULAR_EXPRESSION => '/^[A-Z]{3}$/'
			]
		);

		$validatorResolver->createValidator('TYPO3\CMS\Extbase\Validation\Validator\NumberRangeValidator', [
			'minimum' => 1,
		]);
	}
}
