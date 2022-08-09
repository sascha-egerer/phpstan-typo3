<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Reflection;

use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\ObjectType;
use SaschaEgerer\PhpstanTypo3\Helpers\Typo3ClassNamingUtilityTrait;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\RepositoryInterface;

class RepositoryFindByMethodReflection implements MethodReflection
{

	use Typo3ClassNamingUtilityTrait;

	/** @var \PHPStan\Reflection\ClassReflection */
	private $classReflection;

	/** @var string */
	private $name;

	/** @var ReflectionProvider */
	private $reflectionProvider;

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
		return lcfirst(substr($this->getName(), 6));
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

		if ($modelReflection->hasNativeProperty($this->getPropertyName())) {
			$type = $modelReflection->getNativeProperty($this->getPropertyName())->getReadableType();
		} else {
			$type = new \PHPStan\Type\MixedType(\false);
		}

		return [
			new RepositoryFindByParameterReflection('arg', $type),
		];
	}

	public function isVariadic(): bool
	{
		return false;
	}

	public function getReturnType(): GenericObjectType
	{
		return new GenericObjectType(QueryResultInterface::class, [new ObjectType($this->getModelName())]);
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

	public function getThrowType(): ?\PHPStan\Type\Type
	{
		return null;
	}

	public function hasSideEffects(): TrinaryLogic
	{
		return TrinaryLogic::createNo();
	}

}
