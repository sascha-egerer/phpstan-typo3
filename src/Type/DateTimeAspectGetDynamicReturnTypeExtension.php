<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use TYPO3\CMS\Core\Context\DateTimeAspect;

class DateTimeAspectGetDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return DateTimeAspect::class;
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'get';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): ?\PHPStan\Type\Type
	{
		$firstArgument = $methodCall->args[0];

		if (!$firstArgument instanceof Arg) {
			return null;
		}

		$argumentType = $scope->getType($firstArgument->value);

		if ($argumentType instanceof ConstantStringType) {
			switch ($argumentType->getValue()) {
				case 'timestamp':
				case 'accessTime':
					return new IntegerType();
				case 'iso':
				case 'timezone':
					return new StringType();
				case 'full':
					return new ObjectType(\DateTimeImmutable::class);
			}
		}

		return null;
	}

}
