<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Contract;

use SaschaEgerer\PhpstanTypo3\Service\ServiceDefinition;

interface ServiceDefinitionChecker
{

	public function isPrototype(ServiceDefinition $serviceDefinition): bool;

}
