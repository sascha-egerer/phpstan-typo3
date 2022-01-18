<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class QueryInterfaceDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return QueryInterface::class;
	}

	public function isMethodSupported(
		MethodReflection $methodReflection
	): bool
	{
		return $methodReflection->getName() === 'execute';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$argument = $methodCall->getArgs()[0] ?? null;

		$classReflection = $scope->getClassReflection();

		$queryType = $scope->getType($methodCall->var);
		if ($queryType instanceof GenericObjectType) {
			$modelType = $queryType->getTypes();
		} else {
			if ($classReflection === null) {
				return new ErrorType();
			}

			$modelName = ClassNamingUtility::translateRepositoryNameToModelName(
				$classReflection->getName()
			);

			$modelType = [new ObjectType($modelName)];
		}

		if ($argument !== null) {
			$argType = $scope->getType($argument->value);

			if ($classReflection !== null && $argType instanceof ConstantBooleanType && $argType->getValue() === true) {
				return new ArrayType(new IntegerType(), $modelType[0]);
			}
		}

		return new GenericObjectType(QueryResult::class, $modelType);
	}

}
