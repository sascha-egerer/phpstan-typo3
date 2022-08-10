<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

class RequestDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	/** @var array<string, string> */
	private $requestApiGetAttributeMapping;

	/**
	 * @param array<string, string> $requestApiGetAttributeMapping
	 */
	public function __construct(array $requestApiGetAttributeMapping)
	{
		$this->requestApiGetAttributeMapping = $requestApiGetAttributeMapping;
	}

	public function getClass(): string
	{
		return \TYPO3\CMS\Extbase\Mvc\Request::class;
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$argument = $methodCall->getArgs()[0] ?? null;

		if ($argument === null || !($argument->value instanceof \PhpParser\Node\Scalar\String_)) {
			return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		}

		if (isset($this->requestApiGetAttributeMapping[$argument->value->value])) {
			return new ObjectType($this->requestApiGetAttributeMapping[$argument->value->value]);
		}

		return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
	}

	public function isMethodSupported(
		MethodReflection $methodReflection
	): bool
	{
		return $methodReflection->getName() === 'getAttribute';
	}

}
