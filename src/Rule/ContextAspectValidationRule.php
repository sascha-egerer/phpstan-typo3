<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Rule;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Expr\MethodCall>
 */
class ContextAspectValidationRule implements \PHPStan\Rules\Rule
{

	/** @var array<string, string> */
	private $contextApiGetAspectMapping;

	/**
	 * @param array<string, string> $contextApiGetAspectMapping
	 */
	public function __construct(array $contextApiGetAspectMapping)
	{
		$this->contextApiGetAspectMapping = $contextApiGetAspectMapping;
	}

	public function getNodeType(): string
	{
		return Node\Expr\MethodCall::class;
	}

	/**
	 * @param Node\Expr\MethodCall $node
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if (!$node->name instanceof Node\Identifier) {
			return [];
		}

		$methodReflection = $scope->getMethodReflection($scope->getType($node->var), $node->name->toString());

		if ($methodReflection === null) {
			return [];
		}

		if (!in_array($methodReflection->getName(), ['getAspect', 'getPropertyFromAspect'], true)) {
			return [];
		}

		$declaringClass = $methodReflection->getDeclaringClass();

		if ($declaringClass->getName() !== \TYPO3\CMS\Core\Context\Context::class) {
			return [];
		}

		$argument = $node->getArgs()[0] ?? null;

		if (!($argument instanceof Node\Arg) || !($argument->value instanceof Node\Scalar\String_)) {
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
		))->tip('You should add custom aspects to the typo3.contextApiGetAspectMapping setting.')->build();

		return [$ruleError];
	}

}
