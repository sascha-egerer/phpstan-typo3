<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\IntegerRangeType;
use PHPStan\Type\Type;
use TYPO3\CMS\Core\Utility\MathUtility;

class MathUtilityForceIntegerInRangeDynamicReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    private const DEFAULT_MAX = 2000000000;

    public function getClass(): string
    {
        return MathUtility::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'forceIntegerInRange';
    }

    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): ?Type
    {
        $args = $methodCall->getArgs();

        if (!isset($args[1])) {
            return null;
        }

        $min = $this->resolveIntegerValue($scope->getType($args[1]->value));
        $max = isset($args[2]) ? $this->resolveIntegerValue($scope->getType($args[2]->value)) : self::DEFAULT_MAX;

        if ($min !== null && $max !== null && $min > $max) {
            // forceIntegerInRange() clamps to $min first and to $max afterwards, so $max wins
            return new ConstantIntegerType($max);
        }

        return IntegerRangeType::fromInterval($min, $max);
    }

    private function resolveIntegerValue(Type $type): ?int
    {
        return $type instanceof ConstantIntegerType ? $type->getValue() : null;
    }

}
