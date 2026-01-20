<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerRangeType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
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
		Scope $scope,
	): ?Type
	{
		$firstArgument = $methodCall->args[0];

		if (!$firstArgument instanceof Arg) {
			return null;
		}

		$argumentType = $scope->getType($firstArgument->value);

		if ($argumentType->getConstantStrings() !== []) {
			switch ($argumentType->getConstantStrings()[0]->getValue()) {
				case 'id':
					return IntegerRangeType::createAllGreaterThanOrEqualTo(0);
				case 'username':
					return new StringType();
				case 'isLoggedIn':
				case 'isAdmin':
					return new BooleanType();
				case 'groupIds':
					return new ArrayType(new IntegerType(), IntegerRangeType::fromInterval(-2, null));
				case 'groupNames':
					return new ArrayType(new IntegerType(), new StringType());
			}
		}

		return null;
	}

}
