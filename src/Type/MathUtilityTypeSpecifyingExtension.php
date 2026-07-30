<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
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

	private const METHOD_CAN_BE_INTERPRETED_AS_INTEGER = 'canBeInterpretedAsInteger';
	private const METHOD_CAN_BE_INTERPRETED_AS_FLOAT = 'canBeInterpretedAsFloat';
	private const METHOD_IS_INTEGER_IN_RANGE = 'isIntegerInRange';

	private TypeSpecifier $typeSpecifier;

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
				self::METHOD_CAN_BE_INTERPRETED_AS_INTEGER,
				self::METHOD_CAN_BE_INTERPRETED_AS_FLOAT,
				self::METHOD_IS_INTEGER_IN_RANGE,
			],
			true
		);
	}

	public function specifyTypes(MethodReflection $staticMethodReflection, StaticCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
	{
		if ($staticMethodReflection->getName() === self::METHOD_IS_INTEGER_IN_RANGE) {
			return $this->specifyTypesForIsIntegerInRange($node, $scope);
		}

		if ($staticMethodReflection->getName() === self::METHOD_CAN_BE_INTERPRETED_AS_INTEGER) {
			return $this->specifyTypesForCanBeInterpretedAsInteger($node, $scope);
		}

		return $this->specifyTypesForCanBeInterpretedAsFloat($node, $scope);
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
