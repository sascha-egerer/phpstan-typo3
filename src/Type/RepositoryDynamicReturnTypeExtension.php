<?php
declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Reflection\MethodReflection;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;

class RepositoryDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return \TYPO3\CMS\Extbase\Persistence\RepositoryInterface::class;
    }

    public function isMethodSupported(
        MethodReflection $methodReflection
    ): bool {
        return $methodReflection->getName() === 'findByUid';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        $variableType = $scope->getType($methodCall->var);

        if (!($variableType instanceof ObjectType) || !is_subclass_of($variableType->getClassName(), $this->getClass())) {
            return $methodReflection->getReturnType();
        }

        $modelName = ClassNamingUtility::translateRepositoryNameToModelName($variableType->getClassName());

        return new ObjectType($modelName);
    }
}
