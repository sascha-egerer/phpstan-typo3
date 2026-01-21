<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Rule;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use TYPO3\CMS\Core\Context\Context;

/**
 * @implements Rule<MethodCall>
 */
class ContextAspectValidationRule implements Rule
{
    /**
     * @param array<string, string> $contextApiGetAspectMapping
     */
    public function __construct(private readonly array $contextApiGetAspectMapping) {}

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param Node\Expr\MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Identifier) {
            return [];
        }

        $methodReflection = $scope->getMethodReflection($scope->getType($node->var), $node->name->toString());

        if (!$methodReflection instanceof ExtendedMethodReflection) {
            return [];
        }

        if (!in_array($methodReflection->getName(), ['getAspect', 'getPropertyFromAspect'], true)) {
            return [];
        }

        $declaringClass = $methodReflection->getDeclaringClass();

        if ($declaringClass->getName() !== Context::class) {
            return [];
        }

        $argument = $node->getArgs()[0] ?? null;

        if (!($argument instanceof Arg) || !($argument->value instanceof String_)) {
            return [];
        }

        if (isset($this->contextApiGetAspectMapping[$argument->value->value])) {
            return [];
        }

        $ruleError = RuleErrorBuilder::message(sprintf(
            'There is no aspect "%s" configured so we can\'t figure out the exact type to return when calling %s::%s',
            $argument->value->value,
            $declaringClass->getDisplayName(),
            $methodReflection->getName()
        ))
            ->tip('You should add custom aspects to the typo3.contextApiGetAspectMapping setting.')
            ->identifier('phpstanTypo3.contextAspectValidation')
            ->build();

        return [$ruleError];
    }

}
