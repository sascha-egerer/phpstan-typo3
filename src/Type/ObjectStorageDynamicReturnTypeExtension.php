<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class ObjectStorageDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return ObjectStorage::class;
	}

	public function isMethodSupported(
		MethodReflection $methodReflection
	): bool
	{
		return $methodReflection->getName() === 'offsetGet';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): ?Type
	{
		$firstArgument = $methodCall->args[0] ?? null;

		if (!$firstArgument instanceof Arg) {
			return null;
		}

		$argumentType = $scope->getType($firstArgument->value);

		if ((new StringType())->isSuperTypeOf($argumentType)->yes()) {
			return null;
		}
		if ((new IntegerType())->isSuperTypeOf($argumentType)->yes()) {
			return null;
		}
		return new MixedType();
	}

}
