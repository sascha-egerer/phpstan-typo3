<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Service;

use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use SaschaEgerer\PhpstanTypo3\Contract\ServiceMap;

final class DefaultServiceMap implements ServiceMap
{

	/**
	 * @param ServiceDefinition[] $serviceDefinitions
	 */
	public function __construct(private array $serviceDefinitions)
	{
	}

	public function getServiceDefinitions(): array
	{
		return $this->serviceDefinitions;
	}

	public function getServiceDefinitionById(string $id): ?ServiceDefinition
	{
		return $this->serviceDefinitions[$id] ?? null;
	}

	public function getServiceIdFromNode(Expr $node, Scope $scope): ?string
	{
		$strings = $scope->getType($node)->getConstantStrings();
		return count($strings) === 1 ? $strings[0]->getValue() : null;
	}

}
