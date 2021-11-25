<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class QueryResultToArrayDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
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
		$argument = $methodCall->getArgs()[0] ?? null;

		$classReflection = $scope->getClassReflection();

		$resultType = $scope->getType($methodCall->var);
		if ($resultType instanceof GenericObjectType) {
			$modelType = $resultType->getTypes();
		} else {
			$modelName = ClassNamingUtility::translateRepositoryNameToModelName(
				$classReflection->getName()
			);

			$modelType = [new ObjectType($modelName)];
		}

		return new ArrayType(new IntegerType(), $modelType[0]);
	}
}
