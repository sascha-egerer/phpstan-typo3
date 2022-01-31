<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Reflection;

use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;

class RepositoryCountByMethodReflection implements MethodReflection
{

	/** @var \PHPStan\Reflection\ClassReflection */
	private $classReflection;

	/** @var string */
	private $name;

	public function __construct(ClassReflection $classReflection, string $name)
	{
		$this->classReflection = $classReflection;
		$this->name = $name;
	}

	public function getDeclaringClass(): ClassReflection
	{
		return $this->classReflection;
	}

	public function isStatic(): bool
	{
		return false;
	}

	public function isPrivate(): bool
	{
		return false;
	}

	public function isPublic(): bool
	{
		return true;
	}

	public function getPrototype(): ClassMemberReflection
	{
		return $this;
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return array{0: RepositoryCountByParameterReflection}
	 */
	public function getParameters(): array
	{
		return [new RepositoryCountByParameterReflection('arg', new MixedType(\false))];
	}

	public function isVariadic(): bool
	{
		return false;
	}

	public function getReturnType(): Type
	{
		return new IntegerType();
	}

	/**
	 * @return \PHPStan\Reflection\ParametersAcceptor[]
	 */
	public function getVariants(): array
	{
		return [
			new FunctionVariant(
				TemplateTypeMap::createEmpty(),
				null,
				$this->getParameters(),
				$this->isVariadic(),
				$this->getReturnType()
			),
		];
	}

	public function getDocComment(): ?string
	{
		return null;
	}

	public function isDeprecated(): \PHPStan\TrinaryLogic
	{
		return TrinaryLogic::createNo();
	}

	public function getDeprecatedDescription(): ?string
	{
		return null;
	}

	public function isFinal(): \PHPStan\TrinaryLogic
	{
		return TrinaryLogic::createNo();
	}

	public function isInternal(): \PHPStan\TrinaryLogic
	{
		return TrinaryLogic::createNo();
	}

	public function getThrowType(): ?\PHPStan\Type\Type
	{
		return null;
	}

	public function hasSideEffects(): \PHPStan\TrinaryLogic
	{
		return TrinaryLogic::createNo();
	}

}
