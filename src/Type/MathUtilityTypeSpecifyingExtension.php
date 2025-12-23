<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\LNumber;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\StaticMethodTypeSpecifyingExtension;
use TYPO3\CMS\Core\Utility\MathUtility;

final class MathUtilityTypeSpecifyingExtension implements StaticMethodTypeSpecifyingExtension, TypeSpecifierAwareExtension
{

	private const METHOD_FORCE_INTEGER_IN_RANGE = 'forceIntegerInRange';
	private const METHOD_CONVERT_TO_POSITIVE_INTEGER = 'convertToPositiveInteger';
	private const METHOD_CAN_BE_INTERPRETED_AS_INTEGER = 'canBeInterpretedAsInteger';
	private const METHOD_CAN_BE_INTERPRETED_AS_FLOAT = 'canBeInterpretedAsFloat';
	private const METHOD_IS_INTEGER_IN_RANGE = 'isIntegerInRange';

	private ?TypeSpecifier $typeSpecifier = null;

	public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
	{
		$this->typeSpecifier = $typeSpecifier;
	}

	public function getClass(): string
	{
		return MathUtility::class;
	}

	public function isStaticMethodSupported(MethodReflection $staticMethodReflection, StaticCall $node, TypeSpecifierContext $context): bool
	{
		return in_array(
			$staticMethodReflection->getName(),
			[
				self::METHOD_FORCE_INTEGER_IN_RANGE,
				self::METHOD_CONVERT_TO_POSITIVE_INTEGER,
				self::METHOD_CAN_BE_INTERPRETED_AS_INTEGER,
				self::METHOD_CAN_BE_INTERPRETED_AS_FLOAT,
				self::METHOD_IS_INTEGER_IN_RANGE,
			],
			true
		);
	}

	public function specifyTypes(MethodReflection $staticMethodReflection, StaticCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
	{
		if ($staticMethodReflection->getName() === self::METHOD_FORCE_INTEGER_IN_RANGE) {
			return $this->specifyTypesForForceIntegerInRange($node, $scope);
		}

		if ($staticMethodReflection->getName() === self::METHOD_IS_INTEGER_IN_RANGE) {
			return $this->specifyTypesForIsIntegerInRange($node, $scope);
		}

		if ($staticMethodReflection->getName() === self::METHOD_CONVERT_TO_POSITIVE_INTEGER) {
			return $this->specifyTypesForConvertToPositiveInteger($node, $scope);
		}

		if ($staticMethodReflection->getName() === self::METHOD_CAN_BE_INTERPRETED_AS_INTEGER) {
			return $this->specifyTypesForCanBeInterpretedAsInteger($node, $scope);
		}

		return $this->specifyTypesForCanBeInterpretedAsFloat($node, $scope);
	}

	private function specifyTypesForForceIntegerInRange(StaticCall $node, Scope $scope): SpecifiedTypes
	{
		$parentNode = $node->getAttribute('parent');

		if (!$parentNode instanceof Assign) {
			return new SpecifiedTypes();
		}

		$min = isset($node->getArgs()[1]) ? $node->getArgs()[1]->value : new LNumber(0);
		$max = isset($node->getArgs()[2]) ? $node->getArgs()[2]->value : new LNumber(2000000000);

		return $this->typeSpecifier->specifyTypesInCondition(
			$scope,
			new BooleanAnd(
				new FuncCall(
					new Name('is_int'),
					[new Arg($parentNode->var)]
				),
				new BooleanAnd(
					new GreaterOrEqual(
						$parentNode->var,
						$min
					),
					new SmallerOrEqual(
						$parentNode->var,
						$max
					)
				)
			),
			TypeSpecifierContext::createTruthy()
		);
	}

	private function specifyTypesForIsIntegerInRange(StaticCall $node, Scope $scope): SpecifiedTypes
	{
		$firstArgument = $node->getArgs()[0];
		$firstArgumentType = $scope->getType($firstArgument->value);

		$min = $node->getArgs()[1]->value;
		$max = $node->getArgs()[2]->value;

		if ($firstArgumentType->isString()->no()) {
			$typeCheckFuncCall = new FuncCall(
				new Name('is_int'),
				[$firstArgument]
			);
		} else {
			$typeCheckFuncCall = new BooleanAnd(
				new FuncCall(
					new Name('is_numeric'),
					[$firstArgument]
				),
				new BooleanNot(
					new FuncCall(
						new Name('is_float'),
						[$firstArgument]
					)
				)
			);
		}

		return $this->typeSpecifier->specifyTypesInCondition(
			$scope,
			new BooleanAnd(
				$typeCheckFuncCall,
				new BooleanAnd(
					new GreaterOrEqual(
						$firstArgument->value,
						$min
					),
					new SmallerOrEqual(
						$firstArgument->value,
						$max
					)
				)
			),
			TypeSpecifierContext::createTruthy()
		);
	}

	private function specifyTypesForConvertToPositiveInteger(StaticCall $node, Scope $scope): SpecifiedTypes
	{
		$parentNode = $node->getAttribute('parent');

		if (!$parentNode instanceof Assign) {
			return new SpecifiedTypes();
		}

		return $this->typeSpecifier->specifyTypesInCondition(
			$scope,
			new BooleanAnd(
				new FuncCall(
					new Name('is_int'),
					[new Arg($parentNode)]
				),
				new BooleanAnd(
					new GreaterOrEqual(
						$parentNode->var,
						new LNumber(0)
					),
					new SmallerOrEqual(
						$parentNode->var,
						new LNumber(PHP_INT_MAX)
					)
				)
			),
			TypeSpecifierContext::createTruthy()
		);
	}

	private function specifyTypesForCanBeInterpretedAsInteger(StaticCall $node, Scope $scope): SpecifiedTypes
	{
		$firstArgument = $node->getArgs()[0];

		return $this->typeSpecifier->specifyTypesInCondition(
			$scope,
			new FuncCall(
				new Name('is_numeric'),
				[$firstArgument]
			),
			TypeSpecifierContext::createTruthy()
		);
	}

	private function specifyTypesForCanBeInterpretedAsFloat(StaticCall $node, Scope $scope): SpecifiedTypes
	{
		$firstArgument = $node->getArgs()[0];

		return $this->typeSpecifier->specifyTypesInCondition(
			$scope,
			new FuncCall(
				new Name('is_float'),
				[$firstArgument]
			),
			TypeSpecifierContext::createTruthy()
		);
	}

}
