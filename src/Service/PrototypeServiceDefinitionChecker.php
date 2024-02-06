<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Service;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use SaschaEgerer\PhpstanTypo3\Contract\ServiceDefinitionChecker;

final class PrototypeServiceDefinitionChecker implements ServiceDefinitionChecker
{

	public function isPrototype(ServiceDefinition $serviceDefinition, Node $node): bool
	{
		return !$serviceDefinition->isHasTags() && !$serviceDefinition->isHasMethodCalls() && $this->canBePrototypeClass($node);
	}

	private function extractFirstArgument(StaticCall $node): ?Node
	{
		if (!isset($node->args[0])) {
			return null;
		}

		if (!$node->args[0] instanceof Node\Arg) {
			return null;
		}

		return $node->args[0]->value;
	}

	private function canBePrototypeClass(Node $node): bool
	{
		if (!$node instanceof StaticCall) {
			return false;
		}

		$firstArgument = $this->extractFirstArgument($node);

		if (!$firstArgument instanceof ClassConstFetch) {
			return false;
		}

		if (!$firstArgument->class instanceof Node\Name) {
			return false;
		}

		if ($firstArgument->class->isSpecialClassName()) {
			return false;
		}

		/** @var class-string $className */
		$className = $firstArgument->class->toString();

		$reflection = new \ReflectionClass($className);

		$constructorMethod = $reflection->getConstructor();

		if ($constructorMethod === null) {
			return true;
		}

		$constructorParameters = $constructorMethod->getParameters();
		$hasRequiredParameter = false;
		foreach ($constructorParameters as $constructorParameter) {
			if ($constructorParameter->isOptional()) {
				continue;
			}
			$hasRequiredParameter = true;
		}

		return $hasRequiredParameter === false;
	}

}
