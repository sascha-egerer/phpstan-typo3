<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Rule\ValueObject;

final class ValidatorOptionsConfiguration
{

	/** @var string[] */
	private array $supportedOptions;

	/** @var string[] */
	private array $requriedOptions;

	/**
	 * @param string[] $supportedOptions
	 * @param string[] $requriedOptions
	 */
	public function __construct(array $supportedOptions, array $requriedOptions)
	{
		$this->supportedOptions = $supportedOptions;
		$this->requriedOptions = $requriedOptions;
	}

	public static function empty(): self
	{
		return new self([], []);
	}

	/**
	 * @return string[]
	 */
	public function getSupportedOptions(): array
	{
		return $this->supportedOptions;
	}

	/**
	 * @return string[]
	 */
	public function getRequiredOptions(): array
	{
		return $this->requriedOptions;
	}

}
