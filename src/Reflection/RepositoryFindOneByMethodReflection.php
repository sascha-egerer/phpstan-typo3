<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Reflection;

use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use SaschaEgerer\PhpstanTypo3\Helpers\Typo3ClassNamingUtilityTrait;

class RepositoryFindOneByMethodReflection implements MethodReflection
{

	use Typo3ClassNamingUtilityTrait;

	private ClassReflection $classReflection;

	private string $name;

	private ReflectionProvider $reflectionProvider;

	public function __construct(ClassReflection $classReflection, string $name, ReflectionProvider $reflectionProvider)
	{
		$this->classReflection = $classReflection;
		$this->name = $name;
		$this->reflectionProvider = $reflectionProvider;
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

	private function getPropertyName(): string
	{
		return lcfirst(substr($this->getName(), 9));
	}

	/**
	 * @return class-string
	 */
	private function getModelName(): string
	{
		$className = $this->classReflection->getName();

		return $this->translateRepositoryNameToModelName($className);
	}

	/**
	 * @return RepositoryFindByParameterReflection[]
	 */
	public function getParameters(): array
	{
		$modelReflection = $this->reflectionProvider->getClass($this->getModelName());

		$type = $modelReflection->getNativeProperty($this->getPropertyName())->getReadableType();

		return [
			new RepositoryFindByParameterReflection('arg', $type),
		];
	}

	public function isVariadic(): bool
	{
		return false;
	}

	public function getReturnType(): Type
	{
		return TypeCombinator::addNull(new ObjectType($this->getModelName()));
	}

	/**
	 * @return ParametersAcceptor[]
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

	public function isDeprecated(): TrinaryLogic
	{
		return TrinaryLogic::createNo();
	}

	public function getDeprecatedDescription(): ?string
	{
		return null;
	}

	public function isFinal(): TrinaryLogic
	{
		return TrinaryLogic::createNo();
	}

	public function isInternal(): TrinaryLogic
	{
		return TrinaryLogic::createNo();
	}

	public function getThrowType(): ?Type
	{
		return null;
	}

	public function hasSideEffects(): TrinaryLogic
	{
		return TrinaryLogic::createNo();
	}

}
