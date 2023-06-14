<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerRangeType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use TYPO3\CMS\Core\Context\UserAspect;

class UserAspectGetDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return UserAspect::class;
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
			return match ($argumentType->getValue()) {
				'id' => IntegerRangeType::createAllGreaterThanOrEqualTo(0),
				'username' => new StringType(),
				'isLoggedIn', 'isAdmin' => new BooleanType(),
				'groupIds' => new ArrayType(new IntegerType(), IntegerRangeType::fromInterval(-2, null)),
				'groupNames' => new ArrayType(new IntegerType(), new StringType()),
				default => null,
			};
		}

		return null;
	}

}
