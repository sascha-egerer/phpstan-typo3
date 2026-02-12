<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Service;

use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use SaschaEgerer\PhpstanTypo3\Contract\ServiceMap;

final class FakeServiceMap implements ServiceMap
{
    public function getServiceDefinitions(): array
    {
        return [];
    }

    public function getServiceDefinitionById(string $id): ?ServiceDefinition
    {
        return null;
    }

    public function getServiceIdFromNode(Expr $node, Scope $scope): ?string
    {
        return null;
    }

}
