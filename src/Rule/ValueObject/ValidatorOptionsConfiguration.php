<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Rule\ValueObject;

final class ValidatorOptionsConfiguration
{

	/** @var string[] */
	private array $supportedOptions;

	/** @var string[] */
	private array $requiredOptions;

	/**
	 * @param string[] $supportedOptions
	 * @param string[] $requiredOptions
	 */
	public function __construct(array $supportedOptions, array $requiredOptions)
	{
		$this->supportedOptions = $supportedOptions;
		$this->requiredOptions = $requiredOptions;
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
		return $this->requiredOptions;
	}

}
