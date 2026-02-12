<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Service;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Reflection\ReflectionProvider;
use SaschaEgerer\PhpstanTypo3\Contract\ServiceDefinitionChecker;

final readonly class PrototypeServiceDefinitionChecker implements ServiceDefinitionChecker
{
    public function __construct(private ReflectionProvider $reflectionProvider) {}

    public function isPrototype(ServiceDefinition $serviceDefinition, Node $node): bool
    {
        return !$serviceDefinition->isHasTags() && !$serviceDefinition->isHasMethodCalls() && $this->canBePrototypeClass($node);
    }

    private function extractFirstArgument(StaticCall $node): ?Node
    {
        if (!isset($node->args[0])) {
            return null;
        }

        if (!$node->args[0] instanceof Arg) {
            return null;
        }

        return $node->args[0]->value;
    }

    private function canBePrototypeClass(Node $node): bool
    {
        if (!$node instanceof StaticCall) {
            return false;
        }

        $firstArgument = $this->extractFirstArgument($node);

        if (!$firstArgument instanceof ClassConstFetch) {
            return false;
        }

        if (!$firstArgument->class instanceof Name) {
            return false;
        }

        $className = $firstArgument->class->toString();

        if (!$this->reflectionProvider->hasClass($className)) {
            return false;
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        if (!$classReflection->hasConstructor()) {
            return true;
        }

        $constructorMethod = $classReflection->getConstructor();

        $constructorParameters = $constructorMethod->getVariants();

        $hasRequiredParameter = false;
        foreach ($constructorParameters as $constructorParameter) {
            foreach ($constructorParameter->getParameters() as $parameter) {
                if ($parameter->isOptional()) {
                    continue;
                }

                $hasRequiredParameter = true;
            }
        }

        return $hasRequiredParameter === false;
    }

}
