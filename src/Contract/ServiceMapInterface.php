<?php
declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Contract;

use SaschaEgerer\PhpstanTypo3\Service\ServiceDefinition;

interface ServiceMapInterface
{
	/**
	 * @return ServiceDefinition[]
	 */
	public function getServiceDefinitions(): array;

	public function getServiceDefinitionById(string $id): ?ServiceDefinition;
}
