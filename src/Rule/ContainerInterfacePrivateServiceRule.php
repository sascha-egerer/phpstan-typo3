<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ObjectType;
use Psr\Container\ContainerInterface;
use SaschaEgerer\PhpstanTypo3\Service\NullServiceDefinitionChecker;
use SaschaEgerer\PhpstanTypo3\Service\PrivateServiceAnalyzer;

/**
 * @implements Rule<MethodCall>
 */
final readonly class ContainerInterfacePrivateServiceRule implements Rule
{

	public function __construct(private PrivateServiceAnalyzer $privateServiceAnalyzer)
    {
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

		return $this->privateServiceAnalyzer->analyze(
			$node,
			$scope,
			new NullServiceDefinitionChecker(),
			'phpstanTypo3.containerInterfacePrivateService'
		);
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

		$isPsrContainerType = (new ObjectType(ContainerInterface::class))->isSuperTypeOf($argType);
		$isTestCaseType = (new ObjectType('TYPO3\TestingFramework\Core\Functional\FunctionalTestCase'))->isSuperTypeOf($argType);

		if ($isTestCaseType->yes()) {
			return true;
		}

		return !$isPsrContainerType->yes();
	}

}
