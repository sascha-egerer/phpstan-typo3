<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Service;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;
use SaschaEgerer\PhpstanTypo3\Contract\ServiceDefinitionChecker;
use SaschaEgerer\PhpstanTypo3\Contract\ServiceMap;

final readonly class PrivateServiceAnalyzer
{
    public function __construct(private ServiceMap $serviceMap) {}

    /**
     * @param MethodCall|StaticCall $node
     *
     * @return list<IdentifierRuleError>
     */
    public function analyze(Node $node, Scope $scope, ServiceDefinitionChecker $serviceDefinitionChecker, string $identifier): array
    {
        $serviceId = $this->serviceMap->getServiceIdFromNode($node->getArgs()[0]->value, $scope);

        if ($serviceId === null) {
            return [];
        }

        $serviceDefinition = $this->serviceMap->getServiceDefinitionById($serviceId);

        if (!$serviceDefinition instanceof ServiceDefinition || $serviceDefinition->isPublic()) {
            return [];
        }

        if ($serviceDefinitionChecker->isPrototype($serviceDefinition, $node)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf('Service "%s" is private.', $serviceId))->identifier($identifier)->build(),
        ];
    }

}
