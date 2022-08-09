<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\ValidatorResolverOptionsRule\Fixture;

use SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\ValidatorResolverOptionsRule\Source\CustomValidator;
use TYPO3\CMS\Extbase\Validation\Validator\RegularExpressionValidator;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;

final class CreateValidatorWithMissingRequiredOption
{

	public function __construct()
	{
		$validatorResolver = new ValidatorResolver();
		$validatorResolver->createValidator(
			RegularExpressionValidator::class,
			[]
		);

		$validatorResolver->createValidator(
			'RegularExpression',
			[]
		);

		$validatorResolver->createValidator(
			CustomValidator::class,
			[]
		);
	}

}
