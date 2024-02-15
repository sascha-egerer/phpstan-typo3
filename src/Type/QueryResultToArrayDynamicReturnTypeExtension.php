<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\Generic\TemplateType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeTraverser;
use SaschaEgerer\PhpstanTypo3\Helpers\Typo3ClassNamingUtilityTrait;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class QueryResultToArrayDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	use Typo3ClassNamingUtilityTrait;

	public function getClass(): string
	{
		return QueryResultInterface::class;
	}

	public function isMethodSupported(
		MethodReflection $methodReflection
	): bool
	{
		return $methodReflection->getName() === 'toArray';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$resultType = $scope->getType($methodCall->var);

		if (!($resultType instanceof ObjectType)) {
			$resultType = $this->getGenericTypes(
				$scope->getType($methodCall->var)
			)[0] ?? null;
		}

		if ($resultType instanceof GenericObjectType) {
			$modelType = $resultType->getTypes()[0] ?? new ErrorType();
		} else {
			$declaringClass = $methodReflection->getDeclaringClass();
			$possibleTypes = $declaringClass->getPossiblyIncompleteActiveTemplateTypeMap();
			$type = $possibleTypes->getType('TValue');
			$modelType = $type ?? new ErrorType();
		}

		return new ArrayType(new IntegerType(), $modelType);
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
