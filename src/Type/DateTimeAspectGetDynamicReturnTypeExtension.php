<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use DateTimeImmutable;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
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
	): ?Type
	{
		$firstArgument = $methodCall->args[0];

		if (!$firstArgument instanceof Arg) {
			return null;
		}

		$argumentType = $scope->getType($firstArgument->value);

		if ($argumentType->getConstantStrings() !== []) {
			switch ($argumentType->getConstantStrings()[0]->getValue()) {
				case 'timestamp':
				case 'accessTime':
					return new IntegerType();
				case 'iso':
				case 'timezone':
					return new StringType();
				case 'full':
					return new ObjectType(DateTimeImmutable::class);
			}
		}

		return null;
	}

}
