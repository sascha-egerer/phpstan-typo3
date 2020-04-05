<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

class ContextDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		// ToDo(cms-9): move to ::class constants once support for cms-8 has been removed.
		return '\\TYPO3\\CMS\\Core\\Context\\Context';
	}

	public function isMethodSupported(
		MethodReflection $methodReflection
	): bool
	{
		// ToDo(cms-9): move to ::class constants once support for cms-8 has been removed.
		return interface_exists('\\TYPO3\\CMS\\Core\\Context\\AspectInterface')
			&& $methodReflection->getName() === 'getAspect';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$arg = $methodCall->args[0]->value;
		if (!($arg instanceof \PhpParser\Node\Scalar\String_)) {
			return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		}

		switch ($arg->value) {
			case 'date':
				// ToDo(cms-9): move to ::class constants once support for cms-8 has been removed.
				return new ObjectType('\\TYPO3\\CMS\\Core\\Context\\DateTimeAspect');
			case 'visibility':
				// ToDo(cms-9): move to ::class constants once support for cms-8 has been removed.
				return new ObjectType('\\TYPO3\\CMS\\Core\\Context\\VisibilityAspect');
			case 'backend.user':
			case 'frontend.user':
				// ToDo(cms-9): move to ::class constants once support for cms-8 has been removed.
				return new ObjectType('\\TYPO3\\CMS\\Core\\Context\\UserAspect');
			case 'workspace':
				// ToDo(cms-9): move to ::class constants once support for cms-8 has been removed.
				return new ObjectType('\\TYPO3\\CMS\\Core\\Context\\WorkspaceAspect');
			case 'language':
				// ToDo(cms-9): move to ::class constants once support for cms-8 has been removed.
				return new ObjectType('\\TYPO3\\CMS\\Core\\Context\\LanguageAspect');
			case 'typoscript':
				// ToDo(cms-9): move to ::class constants once support for cms-8 has been removed.
				return new ObjectType('\\TYPO3\\CMS\\Core\\Context\\TypoScriptAspect');
			default:
				return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		}
	}

}
