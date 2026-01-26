<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Service;

use PhpParser\Node;
use SaschaEgerer\PhpstanTypo3\Contract\ServiceDefinitionChecker;

final class NullServiceDefinitionChecker implements ServiceDefinitionChecker
{
    public function isPrototype(ServiceDefinition $serviceDefinition, Node $node): bool
    {
        return false;
    }

}
