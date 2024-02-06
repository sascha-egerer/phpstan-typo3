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

		$xml = @simplexml_load_file($this->containerXmlPath);

		if ($xml === false) {
			throw \SaschaEgerer\PhpstanTypo3\Service\ServiceDefinitionFileException::parseError($this->containerXmlPath);
		}

		/** @var ServiceDefinition[] $serviceDefinitions */
		$serviceDefinitions = [];
		/** @var ServiceDefinition[] $aliases */
		$aliases = [];
		foreach ($xml->services->service as $def) {
			/** @var SimpleXMLElement $attrs */
			$attrs = $def->attributes();
			if (!isset($attrs->id)) {
				continue;
			}

			$tags = $this->createTags($def);

			if (in_array('container.excluded', $tags, true)) {
				continue;
			}

			$serviceDefinition = new ServiceDefinition(
				strpos((string) $attrs->id, '.') === 0 ? substr((string) $attrs->id, 1) : (string) $attrs->id,
				isset($attrs->class) ? (string) $attrs->class : null,
				isset($attrs->public) && (string) $attrs->public === 'true',
				isset($attrs->synthetic) && (string) $attrs->synthetic === 'true',
				isset($attrs->alias) ? (string) $attrs->alias : null,
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
			if ($alias !== null && !isset($serviceDefinitions[$alias])) {
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
	private function createTags(?SimpleXMLElement $def): array
	{
		if (!isset($def->tag)) {
			return [];
		}

		$tagNames = [];

		foreach ($def->tag as $tag) {
			$attributes = $tag->attributes();
			if (!isset($attributes->name)) {
				continue;
			}
			$tagNames[] = (string) $attributes->name;
		}

		return $tagNames;
	}

}
