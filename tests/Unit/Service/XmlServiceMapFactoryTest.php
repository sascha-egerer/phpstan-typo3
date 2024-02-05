<?php
declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use SaschaEgerer\PhpstanTypo3\Contract\ServiceMapInterface;
use SaschaEgerer\PhpstanTypo3\Service\ServiceDefinitionFileException;
use SaschaEgerer\PhpstanTypo3\Service\XmlServiceMapFactory;

final class XmlServiceMapFactoryTest extends TestCase
{
	public function testThatServiceDefinitionsAreEmptyWhenContainerXmlPathIsNull(): void
	{
		self::assertSame([], $this->createServiceMap(null)->getServiceDefinitions());
	}

	public function testThatAnExceptionIsThrownWhenFileDoesNotExist(): void
	{
		$this->expectException(ServiceDefinitionFileException::class);

		$this->createServiceMap(__DIR__ . '/foo.xml');
	}

	public function testThatAnExceptionIsThrownWhenFileCannotBeParsed(): void
	{
		$this->expectException(ServiceDefinitionFileException::class);

		$this->createServiceMap(__DIR__ . '/../Fixtures/container_with_errors.xml');
	}

	public function testThatServiceDefinitionsAreConstructedSuccessfully(): void
	{
		$serviceMap = $this->createServiceMap(__DIR__ . '/../Fixtures/container.xml');

		self::assertCount(7, $serviceMap->getServiceDefinitions());

		self::assertNull($serviceMap->getServiceDefinitionById('foo'));

		$serviceDefinition = $serviceMap->getServiceDefinitionById('public');
		self::assertNotNull($serviceDefinition);
	}

	private function createServiceMap(?string $containerXmlPath): ServiceMapInterface
	{
		return (new XmlServiceMapFactory($containerXmlPath))->create();
	}
}
