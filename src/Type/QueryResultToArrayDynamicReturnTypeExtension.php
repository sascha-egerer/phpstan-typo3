<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
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
		$classReflection = $scope->getClassReflection();

		$resultType = $scope->getType($methodCall->var);
		if ($resultType instanceof GenericObjectType) {
			$modelType = $resultType->getTypes();
		} else {
			if ($classReflection === null) {
				return new ErrorType();
			}

			$modelName = $this->translateRepositoryNameToModelName(
				$classReflection->getName()
			);

			$modelType = [new ObjectType($modelName)];
		}

		return new ArrayType(new IntegerType(), $modelType[0]);
	}

}
