<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GeneralUtilityGetIndpEnvDynamicReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return GeneralUtility::class;
	}

	public function isStaticMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'getIndpEnv';
	}

	public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): ?Type
	{
		$firstArgument = $methodCall->args[0];

		if (!$firstArgument instanceof Arg) {
			return null;
		}

		$argumentType = $scope->getType($firstArgument->value);

		if (!$argumentType instanceof ConstantStringType) {
			return null;
		}

		if ($argumentType->getValue() === '_ARRAY') {
			return new ArrayType(new StringType(), new UnionType([new StringType(), new BooleanType()]));
		}

		if (in_array($argumentType->getValue(), ['TYPO3_SSL', 'TYPO3_PROXY', 'TYPO3_REV_PROXY'], true)) {
			return new BooleanType();
		}

		return new StringType();
	}

}
