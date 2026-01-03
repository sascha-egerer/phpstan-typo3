<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Rule\ValueObject;

final readonly class ValidatorOptionsConfiguration
{

	/**
	 * @param string[] $supportedOptions
	 * @param string[] $requiredOptions
	 */
	public function __construct(private array $supportedOptions, private array $requiredOptions)
    {
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
