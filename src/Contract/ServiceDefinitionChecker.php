<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Contract;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use SaschaEgerer\PhpstanTypo3\Service\ServiceDefinition;

interface ServiceDefinitionChecker
{

	/**
	 * @param Node\Expr\MethodCall|StaticCall $node
	 */
	public function isPrototype(ServiceDefinition $serviceDefinition, Node $node): bool;

}
