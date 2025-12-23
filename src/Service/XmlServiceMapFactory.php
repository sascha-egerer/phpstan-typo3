<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Service;

use SaschaEgerer\PhpstanTypo3\Contract\ServiceMap;
use SaschaEgerer\PhpstanTypo3\Contract\ServiceMapFactory;
use SimpleXMLElement;

final class XmlServiceMapFactory implements ServiceMapFactory
{

	private ?string $containerXmlPath;

	public function __construct(?string $containerXmlPath)
	{
		$this->containerXmlPath = $containerXmlPath;
	}

	public function create(): ServiceMap
	{
		if ($this->containerXmlPath === null) {
			return new FakeServiceMap();
		}

		if (!file_exists($this->containerXmlPath)) {
			throw \SaschaEgerer\PhpstanTypo3\Service\ServiceDefinitionFileException::notFound($this->containerXmlPath);
		}

		$containerXml = file_get_contents($this->containerXmlPath);

		if ($containerXml === false) {
			throw \SaschaEgerer\PhpstanTypo3\Service\ServiceDefinitionFileException::notReadable($this->containerXmlPath);
		}

		$xml = @simplexml_load_string($containerXml);

		if ($xml === false) {
			throw \SaschaEgerer\PhpstanTypo3\Service\ServiceDefinitionFileException::parseError($this->containerXmlPath);
		}

		/** @var ServiceDefinition[] $serviceDefinitions */
		$serviceDefinitions = [];
		/** @var ServiceDefinition[] $aliases */
		$aliases = [];
		foreach ($xml->services->service as $def) {
			$attrs = $def->attributes();
			if ($attrs === null) {
				continue;
			}

			$attributesArray = ((array) $attrs)['@attributes'] ?? [];

			if (!is_scalar($attributesArray['id'] ?? null)) {
				continue;
			}
			$id = (string) $attributesArray['id'];
			if ($id === '') {
				continue;
			}

			$tags = $this->createTags($def);

			if (in_array('container.excluded', $tags, true)) {
				continue;
			}

			$serviceDefinition = new ServiceDefinition(
				ltrim($id, '.'),
				isset($attributesArray['class']) ? (string) $attributesArray['class'] : null,
				($attributesArray['public'] ?? null) === 'true',
				($attributesArray['synthetic'] ?? null) === 'true',
				isset($attributesArray['alias']) ? (string) $attributesArray['alias'] : null,
				isset($def->argument),
				isset($def->call),
				isset($def->tag),
			);

			if ($serviceDefinition->getAlias() !== null) {
				$aliases[] = $serviceDefinition;
			} else {
				$serviceDefinitions[$serviceDefinition->getId()] = $serviceDefinition;
			}
		}
		foreach ($aliases as $serviceDefinition) {
			$alias = $serviceDefinition->getAlias();
			if ($alias === null) {
				continue;
			}
			if (!isset($serviceDefinitions[$alias])) {
				continue;
			}
			$id = $serviceDefinition->getId();
			$serviceDefinitions[$id] = new ServiceDefinition(
				$id,
				$serviceDefinitions[$alias]->getClass(),
				$serviceDefinition->isPublic(),
				$serviceDefinition->isSynthetic(),
				$alias,
				$serviceDefinition->isHasConstructorArguments(),
				$serviceDefinition->isHasMethodCalls(),
				$serviceDefinition->isHasTags()
			);
		}

		return new DefaultServiceMap($serviceDefinitions);
	}

	/**
	 * @return string[]
	 */
	private function createTags(SimpleXMLElement $def): array
	{
		if (!isset($def->tag)) {
			return [];
		}

		$tagNames = [];

		foreach ($def->tag as $tag) {
			$name = (string) $tag->attributes()?->name;
			if ($name === '') {
				continue;
			}
			$tagNames[] = $name;
		}

		return $tagNames;
	}

}
