<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Context\TypoScriptAspect;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Context\VisibilityAspect;
use TYPO3\CMS\Core\Context\WorkspaceAspect;

class ContextDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return Context::class;
	}

	public function isMethodSupported(
		MethodReflection $methodReflection
	): bool
	{
		return $methodReflection->getName() === 'getAspect';
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
				return new ObjectType(DateTimeAspect::class);
			case 'visibility':
				return new ObjectType(VisibilityAspect::class);
			case 'backend.user':
			case 'frontend.user':
				return new ObjectType(UserAspect::class);
			case 'workspace':
				return new ObjectType(WorkspaceAspect::class);
			case 'language':
				return new ObjectType(LanguageAspect::class);
			case 'typoscript':
				return new ObjectType(TypoScriptAspect::class);
			default:
				return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		}
	}

}
