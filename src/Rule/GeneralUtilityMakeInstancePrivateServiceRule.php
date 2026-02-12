<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use SaschaEgerer\PhpstanTypo3\Service\PrivateServiceAnalyzer;
use SaschaEgerer\PhpstanTypo3\Service\PrototypeServiceDefinitionChecker;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @implements Rule<StaticCall>
 */
final readonly class GeneralUtilityMakeInstancePrivateServiceRule implements Rule
{
    public function __construct(private PrivateServiceAnalyzer $privateServiceAnalyzer, private PrototypeServiceDefinitionChecker $prototypeServiceDefinitionChecker) {}

    public function getNodeType(): string
    {
        return StaticCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip($node)) {
            return [];
        }

        return $this->privateServiceAnalyzer->analyze(
            $node,
            $scope,
            $this->prototypeServiceDefinitionChecker,
            'phpstanTypo3.generalUtilityMakeInstancePrivateService'
        );
    }

    private function shouldSkip(StaticCall $node): bool
    {
        if (!$node->name instanceof Identifier) {
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

        if (!$node->class instanceof FullyQualified) {
            return true;
        }

        return $node->class->toString() !== GeneralUtility::class;
    }

}
