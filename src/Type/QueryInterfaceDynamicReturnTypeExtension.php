<?php
declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ArrayType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Reflection\MethodReflection;
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
    ): bool {
        return $methodReflection->getName() === 'execute';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        if (isset($methodCall->args[0])) {
            $arg = $methodCall->args[0]->value;

            if ((string)$arg->name === 'true') {
                $modelName = ClassNamingUtility::translateRepositoryNameToModelName(
                    $scope->getClassReflection()->getName()
                );

                return new ArrayType(new ObjectType($modelName));
            }
        }

        return new ObjectType(QueryResult::class);
    }
}
