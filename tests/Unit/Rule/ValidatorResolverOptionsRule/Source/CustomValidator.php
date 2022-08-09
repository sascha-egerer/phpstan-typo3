<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\ValidatorResolverOptionsRule\Source;

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

final class CustomValidator extends AbstractValidator
{

	private const MY_OPTION = 'my-option';

	protected $supportedOptions = [
		self::MY_OPTION => [0, 'An option', 'integer', true],
	];

	protected function isValid($value): void
	{
	}

}
