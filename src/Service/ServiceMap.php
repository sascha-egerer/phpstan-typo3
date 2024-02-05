<?php
declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Service;

use SaschaEgerer\PhpstanTypo3\Contract\ServiceMapInterface;

final class ServiceMap implements ServiceMapInterface
{
	/**
	 * @var ServiceDefinition[]
	 */
	private array $serviceDefinitions;

	/**
	 * @param ServiceDefinition[] $serviceDefinitions
	 */
	public function __construct(array $serviceDefinitions)
	{
		$this->serviceDefinitions = $serviceDefinitions;
	}

	public function getServiceDefinitions(): array
	{
		return $this->serviceDefinitions;
	}

	public function getServiceDefinitionById(string $id): ?ServiceDefinition
	{
		return $this->serviceDefinitions[$id] ?? null;
	}
}
