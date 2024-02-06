<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ObjectType;
use SaschaEgerer\PhpstanTypo3\Service\NullServiceDefinitionChecker;
use SaschaEgerer\PhpstanTypo3\Service\PrivateServiceAnalyzer;

/**
 * @implements Rule<MethodCall>
 */
final class ContainerInterfacePrivateServiceRule implements Rule
{

	private PrivateServiceAnalyzer $privateServiceAnalyzer;

	public function __construct(PrivateServiceAnalyzer $privateServiceAnalyzer)
	{
		$this->privateServiceAnalyzer = $privateServiceAnalyzer;
	}

	public function getNodeType(): string
	{
		return MethodCall::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($this->shouldSkip($node, $scope)) {
			return [];
		}

		return $this->privateServiceAnalyzer->analyze($node, $scope, new NullServiceDefinitionChecker());
	}

	private function shouldSkip(MethodCall $node, Scope $scope): bool
	{
		if (!$node->name instanceof Node\Identifier) {
			return true;
		}

		$methodCallArguments = $node->getArgs();

		if (!isset($methodCallArguments[0])) {
			return true;
		}

		$methodCallName = $node->name->name;

		if ($methodCallName !== 'get') {
			return true;
		}

		$argType = $scope->getType($node->var);

		$isPsrContainerType = (new ObjectType('Psr\Container\ContainerInterface'))->isSuperTypeOf($argType);

		return !$isPsrContainerType->yes();
	}

}
