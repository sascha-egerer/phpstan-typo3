<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

/**
 * @deprecated This class will be dropped once support for TYPO3 <= 10 is dropped.
 */
class ObjectManagerDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return interface_exists(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface::class)
			? \TYPO3\CMS\Extbase\Object\ObjectManagerInterface::class : '';
	}

	public function isMethodSupported(
		MethodReflection $methodReflection
	): bool
	{
		return interface_exists(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface::class)
			&& $methodReflection->getName() === 'get';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$argument = $methodCall->getArgs()[0] ?? null;

		if ($argument === null) {
			return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		}

		$argumentValue = $argument->value;

		if (!($argumentValue instanceof \PhpParser\Node\Expr\ClassConstFetch)) {
			return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		}
		/** @var Name $class */
		$class = $argumentValue->class;

		return new ObjectType((string) $class);
	}

}
