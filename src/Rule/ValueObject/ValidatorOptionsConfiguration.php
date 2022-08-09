<?php
declare(strict_types=1);


namespace SaschaEgerer\PhpstanTypo3\Rule\ValueObject;


final class ValidatorOptionsConfiguration
{
	private array $supportedOptions;
	private array $requriedOptions;

	public function __construct(array $supportedOptions, array $requriedOptions)
	{
		$this->supportedOptions = $supportedOptions;
		$this->requriedOptions = $requriedOptions;
	}

	public static function empty(): self
	{
		return new self([], []);
	}

	public function getSupportedOptions(): array
	{
		return $this->supportedOptions;
	}

	public function getRequriedOptions(): array
	{
		return $this->requriedOptions;
	}
}
