<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use SaschaEgerer\PhpstanTypo3\Contract\ServiceMap;
use SaschaEgerer\PhpstanTypo3\Service\XmlServiceMapFactory;

final class XmlServiceMapFactoryTest extends TestCase
{

	public function testThatServiceDefinitionsAreEmptyWhenContainerXmlPathIsNull(): void
	{
		$this->assertSame([], $this->createServiceMap(null)->getServiceDefinitions());
	}

	public function testThatAnExceptionIsThrownWhenFileDoesNotExist(): void
	{
		$this->expectException(\SaschaEgerer\PhpstanTypo3\Service\ServiceDefinitionFileException::class);

		$this->createServiceMap(__DIR__ . '/foo.xml');
	}

	public function testThatAnExceptionIsThrownWhenFileCannotBeParsed(): void
	{
		$this->expectException(\SaschaEgerer\PhpstanTypo3\Service\ServiceDefinitionFileException::class);

		$this->createServiceMap(__DIR__ . '/../Fixtures/container_with_errors.xml');
	}

	public function testThatServiceDefinitionsAreConstructedSuccessfully(): void
	{
		$serviceMap = $this->createServiceMap(__DIR__ . '/../Fixtures/container.xml');

		$this->assertCount(8, $serviceMap->getServiceDefinitions());

		$this->assertNotInstanceOf(\SaschaEgerer\PhpstanTypo3\Service\ServiceDefinition::class, $serviceMap->getServiceDefinitionById('foo'));

		$serviceDefinition = $serviceMap->getServiceDefinitionById('public');
		$this->assertInstanceOf(\SaschaEgerer\PhpstanTypo3\Service\ServiceDefinition::class, $serviceDefinition);

		$serviceDefinitionExcluded = $serviceMap->getServiceDefinitionById('excluded');
		$this->assertNotInstanceOf(\SaschaEgerer\PhpstanTypo3\Service\ServiceDefinition::class, $serviceDefinitionExcluded);
	}

	private function createServiceMap(?string $containerXmlPath): ServiceMap
	{
		return (new XmlServiceMapFactory($containerXmlPath))->create();
	}

}
