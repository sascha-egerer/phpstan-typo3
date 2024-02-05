<?php
declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Service;

use SaschaEgerer\PhpstanTypo3\Contract\ServiceMapInterface;

final class FakeServiceMap implements ServiceMapInterface
{

	public function getServiceDefinitions(): array
	{
		return [];
	}

	public function getServiceDefinitionById(string $id): ?ServiceDefinition
	{
		return null;
	}
}
