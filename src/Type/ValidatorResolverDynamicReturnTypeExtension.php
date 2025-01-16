<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;

class ValidatorResolverDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return ValidatorResolver::class;
	}

	public function isMethodSupported(
		MethodReflection $methodReflection
	): bool
	{
		return $methodReflection->getName() === 'createValidator';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$argument = $methodCall->getArgs()[0] ?? null;

		if ($argument === null) {
			return $methodReflection->getVariants()[0]->getReturnType();
		}

		$argumentValue = $argument->value;

		if (!($argumentValue instanceof ClassConstFetch)) {
			return $methodReflection->getVariants()[0]->getReturnType();
		}
		/** @var Name $class */
		$class = $argumentValue->class;

		return TypeCombinator::addNull(new ObjectType((string) $class));
	}

}
