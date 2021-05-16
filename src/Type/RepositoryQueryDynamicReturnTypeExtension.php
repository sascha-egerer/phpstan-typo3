<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class RepositoryQueryDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return \TYPO3\CMS\Extbase\Persistence\RepositoryInterface::class;
	}

	public function isMethodSupported(
		MethodReflection $methodReflection
	): bool
	{
		return $methodReflection->getName() === 'createQuery';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$variableType = $scope->getType($methodCall->var);

		$modelName = ClassNamingUtility::translateRepositoryNameToModelName($variableType->getClassName());

		return new GenericObjectType(QueryInterface::class, [new ObjectType($modelName)]);
	}

}
