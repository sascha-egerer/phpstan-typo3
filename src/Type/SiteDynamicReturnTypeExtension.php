<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;

class SiteDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	/** @var array<string, string> */
	private $siteApiGetAttributeMapping;

	/** @var TypeStringResolver */
	private $typeStringResolver;

	/**
	 * @param array<string, string> $siteApiGetAttributeMapping
	 */
	public function __construct(array $siteApiGetAttributeMapping, TypeStringResolver $typeStringResolver)
	{
		$this->siteApiGetAttributeMapping = $siteApiGetAttributeMapping;
		$this->typeStringResolver = $typeStringResolver;
	}

	public function getClass(): string
	{
		return \TYPO3\CMS\Core\Site\Entity\Site::class;
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

		if (isset($this->siteApiGetAttributeMapping[$argument->value->value])) {
			return $this->typeStringResolver->resolve($this->siteApiGetAttributeMapping[$argument->value->value]);
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
