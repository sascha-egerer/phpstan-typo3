<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Reflection;

use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Type\Type;

class RepositoryCountByParameterReflection implements ParameterReflection
{

	/** @var string */
	private $name;

	/** @var Type */
	private $type;

	public function __construct(string $name, Type $type)
	{
		$this->name = $name;
		$this->type = $type;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function isOptional(): bool
	{
		return false;
	}

	public function getType(): Type
	{
		return $this->type;
	}

	public function isVariadic(): bool
	{
		return false;
	}

	public function passedByReference(): PassedByReference
	{
		return PassedByReference::createNo();
	}

	public function getDefaultValue(): ?Type
	{
		return null;
	}

}
