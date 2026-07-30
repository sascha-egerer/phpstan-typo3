<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\IntegerRangeType;
use PHPStan\Type\Type;
use TYPO3\CMS\Core\Utility\MathUtility;

final class MathUtilityDynamicReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{

	private const METHOD_FORCE_INTEGER_IN_RANGE = 'forceIntegerInRange';
	private const METHOD_CONVERT_TO_POSITIVE_INTEGER = 'convertToPositiveInteger';
	private const DEFAULT_MAX = 2000000000;

	public function getClass(): string
	{
		return MathUtility::class;
	}

	public function isStaticMethodSupported(MethodReflection $methodReflection): bool
	{
		return in_array(
			$methodReflection->getName(),
			[
				self::METHOD_FORCE_INTEGER_IN_RANGE,
				self::METHOD_CONVERT_TO_POSITIVE_INTEGER,
			],
			true
		);
	}

	public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): ?Type
	{
		if ($methodReflection->getName() === self::METHOD_CONVERT_TO_POSITIVE_INTEGER) {
			return IntegerRangeType::fromInterval(0, null);
		}

		$args = $methodCall->getArgs();

		if (!isset($args[1])) {
			return null;
		}

		[$minLowerBound] = $this->resolveBounds($scope->getType($args[1]->value));
		[$maxLowerBound, $maxUpperBound] = isset($args[2])
			? $this->resolveBounds($scope->getType($args[2]->value))
			: [self::DEFAULT_MAX, self::DEFAULT_MAX];

		// forceIntegerInRange() clamps to $min first and to $max afterwards, so with
		// $max < $min the result drops below $min and $max becomes the effective minimum
		$lowerBound = $minLowerBound !== null && $maxLowerBound !== null
			? min($minLowerBound, $maxLowerBound)
			: null;

		return IntegerRangeType::fromInterval($lowerBound, $maxUpperBound);
	}

	/**
	 * @return array{?int, ?int}
	 */
	private function resolveBounds(Type $type): array
	{
		if ($type instanceof ConstantIntegerType) {
			return [$type->getValue(), $type->getValue()];
		}

		if ($type instanceof IntegerRangeType) {
			return [$type->getMin(), $type->getMax()];
		}

		return [null, null];
	}

}
