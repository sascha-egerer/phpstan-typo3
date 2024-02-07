<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Rule;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @implements Rule<MethodCall>
 */
class RequestAttributeValidationRule implements Rule
{

	/** @var array<string, string> */
	private array $requestGetAttributeMapping;

	/**
	 * @param array<string, string> $requestGetAttributeMapping
	 */
	public function __construct(array $requestGetAttributeMapping)
	{
		$this->requestGetAttributeMapping = $requestGetAttributeMapping;
	}

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
		if ($methodReflection === null || $methodReflection->getName() !== 'getAttribute') {
			return [];
		}

		$declaringClass = $methodReflection->getDeclaringClass();

		if (interface_exists(ServerRequestInterface::class)) {
			if (!$declaringClass->implementsInterface(ServerRequestInterface::class)
				&& $declaringClass->getName() !== ServerRequestInterface::class) {
				return [];
			}
		}

		$argument = $node->getArgs()[0] ?? null;

		if (!($argument instanceof Arg) || !($argument->value instanceof String_)) {
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
