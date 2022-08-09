<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\ValidatorResolverOptionsRule\Source;

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class CustomValidatorWithoutOptions extends AbstractValidator
{

	/**
	 * @param mixed $value
	 */
	protected function isValid($value): void
	{
	}

}
