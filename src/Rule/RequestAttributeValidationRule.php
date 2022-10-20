<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Rule;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Expr\MethodCall>
 */
class RequestAttributeValidationRule implements \PHPStan\Rules\Rule
{

	/** @var array<string, string> */
	private $requestGetAttributeMapping;

	/**
	 * @param array<string, string> $requestGetAttributeMapping
	 */
	public function __construct(array $requestGetAttributeMapping)
	{
		$this->requestGetAttributeMapping = $requestGetAttributeMapping;
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
		if ($methodReflection === null || $methodReflection->getName() !== 'getAttribute') {
			return [];
		}

		$declaringClass = $methodReflection->getDeclaringClass();

		if (!$declaringClass->implementsInterface(ServerRequestInterface::class) && $declaringClass->getName() !== ServerRequestInterface::class) {
			return [];
		}

		$argument = $node->getArgs()[0] ?? null;

		if (!($argument instanceof Node\Arg) || !($argument->value instanceof Node\Scalar\String_)) {
			return [];
		}

		if (isset($this->requestGetAttributeMapping[$argument->value->value])) {
			return [];
		}

		$ruleError = RuleErrorBuilder::message(sprintf(
			'There is no request attribute "%s" configured so we can\'t figure out the exact type to return when calling %s::%s',
			$argument->value->value,
			$declaringClass->getDisplayName(),
			$methodReflection->getName()
		))->tip('You should add custom request attribute to the typo3.requestGetAttributeMapping setting.')->build();

		return [$ruleError];
	}

}
