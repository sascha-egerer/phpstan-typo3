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
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @implements Rule<MethodCall>
 */
class SiteAttributeValidationRule implements Rule
{

	/** @var array<string, string> */
	private array $siteGetAttributeMapping;

	/**
	 * @param array<string, string> $siteGetAttributeMapping
	 */
	public function __construct(array $siteGetAttributeMapping)
	{
		$this->siteGetAttributeMapping = $siteGetAttributeMapping;
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

		if ($declaringClass->getName() !== Site::class) {
			return [];
		}

		$argument = $node->getArgs()[0] ?? null;

		if (!($argument instanceof Arg) || !($argument->value instanceof String_)) {
			return [];
		}

		if (isset($this->siteGetAttributeMapping[$argument->value->value])) {
			return [];
		}

		$ruleError = RuleErrorBuilder::message(sprintf(
			'There is no site attribute "%s" configured so we can\'t figure out the exact type to return when calling %s::%s',
			$argument->value->value,
			$declaringClass->getDisplayName(),
			$methodReflection->getName()
		))->tip('You should add custom site attribute to the typo3.siteGetAttributeMapping setting.')->build();

		return [$ruleError];
	}

}
