<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\ValidatorResolverOptionsRule\Source;

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class CustomValidatorWithOptions extends AbstractValidator
{

	private const MY_OPTION = 'my-option';

	/** @var array[] */
	protected $supportedOptions = [
		self::MY_OPTION => [0, 'An option', 'integer', true],
	];

	/**
	 * @param mixed $value
	 */
	protected function isValid($value): void
	{
	}

}
