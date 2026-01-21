<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;
use TYPO3\CMS\Core\Site\Entity\Site;

class SiteDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    /**
     * @param array<string, string> $siteGetAttributeMapping
     */
    public function __construct(private array $siteGetAttributeMapping, private readonly TypeStringResolver $typeStringResolver) {}

    public function getClass(): string
    {
        return Site::class;
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope,
    ): Type {
        $argument = $methodCall->getArgs()[0] ?? null;

        if ($argument === null || !($argument->value instanceof String_)) {
            return $methodReflection->getVariants()[0]->getReturnType();
        }

        if (isset($this->siteGetAttributeMapping[$argument->value->value])) {
            return $this->typeStringResolver->resolve($this->siteGetAttributeMapping[$argument->value->value]);
        }

        return $methodReflection->getVariants()[0]->getReturnType();
    }

    public function isMethodSupported(
        MethodReflection $methodReflection,
    ): bool {
        return $methodReflection->getName() === 'getAttribute';
    }

}
