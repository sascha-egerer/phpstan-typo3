<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;
use SaschaEgerer\PhpstanTypo3\Helpers\TypeNamingUtility;

class SiteDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	/** @var array<string, string> */
	private $siteApiGetAttributeMapping;

	/**
	 * @param array<string, string> $siteApiGetAttributeMapping
	 */
	public function __construct(array $siteApiGetAttributeMapping)
	{
		$this->siteApiGetAttributeMapping = $siteApiGetAttributeMapping;
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

			$phpType = TypeNamingUtility::translateTypeNameToType($this->siteApiGetAttributeMapping[$argument->value->value]);
			if ($phpType !== null) {
				return $phpType;
			}
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
