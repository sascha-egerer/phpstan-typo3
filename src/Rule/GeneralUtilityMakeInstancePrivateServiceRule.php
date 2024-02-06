<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use SaschaEgerer\PhpstanTypo3\Service\PrivateServiceAnalyzer;
use SaschaEgerer\PhpstanTypo3\Service\PrototypeServiceDefinitionChecker;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @implements Rule<StaticCall>
 */
final class GeneralUtilityMakeInstancePrivateServiceRule implements Rule
{

	private PrivateServiceAnalyzer $privateServiceAnalyzer;

	public function __construct(PrivateServiceAnalyzer $privateServiceAnalyzer)
	{
		$this->privateServiceAnalyzer = $privateServiceAnalyzer;
	}

	public function getNodeType(): string
	{
		return StaticCall::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($this->shouldSkip($node)) {
			return [];
		}

		return $this->privateServiceAnalyzer->analyze($node, $scope, new PrototypeServiceDefinitionChecker());
	}

	private function shouldSkip(StaticCall $node): bool
	{
		if (!$node->name instanceof Node\Identifier) {
			return true;
		}

		$methodCallArguments = $node->getArgs();

		if (!isset($methodCallArguments[0])) {
			return true;
		}

		$methodCallName = $node->name->name;

		if ($methodCallName !== 'makeInstance') {
			return true;
		}

		if (count($methodCallArguments) > 1) {
			return true;
		}

		if (!$node->class instanceof Node\Name\FullyQualified) {
			return true;
		}

		return $node->class->toString() !== GeneralUtility::class;
	}

}
