<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Service;

use SaschaEgerer\PhpstanTypo3\Contract\ServiceDefinitionChecker;

final class PrototypeServiceDefinitionChecker implements ServiceDefinitionChecker
{

	public function isPrototype(ServiceDefinition $serviceDefinition): bool
	{
		return !$serviceDefinition->isHasTags() && !$serviceDefinition->isHasMethodCalls() && !$serviceDefinition->isHasConstructorArguments();
	}

}
