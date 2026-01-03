<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;
use TYPO3\CMS\Core\Context\Context;

class ContextDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	/**
	 * @param array<string, string> $contextApiGetAspectMapping
	 */
	public function __construct(private array $contextApiGetAspectMapping, private readonly TypeStringResolver $typeStringResolver)
    {
    }

	public function getClass(): string
	{
		return Context::class;
	}

	public function isMethodSupported(
		MethodReflection $methodReflection,
	): bool
	{
		return $methodReflection->getName() === 'getAspect';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope,
	): Type
	{
		$argument = $methodCall->getArgs()[0] ?? null;

		if ($argument === null || !($argument->value instanceof String_)) {
			return $methodReflection->getVariants()[0]->getReturnType();
		}

		if (isset($this->contextApiGetAspectMapping[$argument->value->value])) {
			return $this->typeStringResolver->resolve($this->contextApiGetAspectMapping[$argument->value->value]);
		}

		return $methodReflection->getVariants()[0]->getReturnType();
	}

}
