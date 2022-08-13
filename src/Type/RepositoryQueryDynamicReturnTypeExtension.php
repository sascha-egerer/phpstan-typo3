<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use SaschaEgerer\PhpstanTypo3\Helpers\Typo3ClassNamingUtilityTrait;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class RepositoryQueryDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
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
		return $methodReflection->getName() === 'createQuery';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$queryType = $scope->getType($methodCall->var);
		if ($queryType instanceof GenericObjectType) {
			$modelType = $queryType->getTypes();
		} else {
			$variableType = $scope->getType($methodCall->var);

			if (!$variableType instanceof TypeWithClassName) {
				return new ErrorType();
			}

			/** @var class-string $className */
			$className = $variableType->getClassName();

			$modelName = $this->translateRepositoryNameToModelName($className);

			$modelType = [new ObjectType($modelName)];
		}

		return new GenericObjectType(QueryInterface::class, $modelType);
	}

}
