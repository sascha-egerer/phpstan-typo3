<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\Tag\ExtendsTag;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\Generic\TemplateType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeTraverser;
use PHPStan\Type\TypeWithClassName;
use SaschaEgerer\PhpstanTypo3\Helpers\Typo3ClassNamingUtilityTrait;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class RepositoryFindAllDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	use Typo3ClassNamingUtilityTrait;

	public function getClass(): string
	{
		return \TYPO3\CMS\Extbase\Persistence\RepositoryInterface::class;
	}

	public function isMethodSupported(
		MethodReflection $methodReflection
	): bool
	{
		return $methodReflection->getName() === 'findAll';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$variableType = $scope->getType($methodCall->var);
		$methodReturnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		if (!$variableType instanceof TypeWithClassName) {
			return $methodReturnType;
		}

		$methodReturnTypeGeneric = $this->getGenericTypes($methodReturnType)[0] ?? null;
		if (
			$methodReturnTypeGeneric instanceof GenericObjectType &&
			($methodReturnTypeGeneric->getTypes()[0] ?? null) instanceof ObjectType &&
			$methodReturnTypeGeneric->getTypes()[0]->getClassName() !== DomainObjectInterface::class
		) {
			if ($methodReflection->getDeclaringClass()->getName() !== Repository::class) {
				return $methodReturnType;
			}
			return $methodReturnTypeGeneric;
		}

		/** @var class-string $className */
		$className = $variableType->getClassName();

		// if we have a custom findAll method...
		if ($methodReflection->getDeclaringClass()->getName() !== Repository::class) {
			if ($methodReturnType->getIterableValueType() instanceof ObjectType) {
				return $methodReturnType;
			}

			if ($variableType->getClassReflection() !== null) {
				$repositoryExtendsTags = $variableType->getClassReflection()->getExtendsTags()[Repository::class] ?? null;
				if ($repositoryExtendsTags instanceof ExtendsTag && $repositoryExtendsTags->getType() instanceof GenericObjectType) {
					return new GenericObjectType(QueryResultInterface::class, [$repositoryExtendsTags->getType()->getTypes()[0] ?? new ErrorType()]);
				}
			}
			/** @var class-string $className */
			$className = $methodReflection->getDeclaringClass()->getName();
		}

		$modelName = $this->translateRepositoryNameToModelName($className);

		return new GenericObjectType(QueryResultInterface::class, [new ObjectType($modelName)]);
	}

	/**
	 * @return GenericObjectType[]
	 */
	private function getGenericTypes(Type $baseType): array
	{
		$genericObjectTypes = [];
		TypeTraverser::map($baseType, static function (Type $type, callable $traverse) use (&$genericObjectTypes): Type {
			if ($type instanceof GenericObjectType) {
				$resolvedType =	TypeTraverser::map($type, static function (Type $type, callable $traverse): Type {
					if ($type instanceof TemplateType) {
						return $traverse($type->getBound());
					}
					return $traverse($type);
				});
				if (!$resolvedType instanceof GenericObjectType) {
					throw new \PHPStan\ShouldNotHappenException();
				}
				$genericObjectTypes[] = $resolvedType;
				$traverse($type);
				return $type;
			}
			$traverse($type);
			return $type;
		});

		return $genericObjectTypes;
	}

}
