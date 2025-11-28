<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Service;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Testing\PHPStanTestCase;
use SaschaEgerer\PhpstanTypo3\Service\PrototypeServiceDefinitionChecker;
use SaschaEgerer\PhpstanTypo3\Service\ServiceDefinition;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;

final class PrototypeServiceDefinitionCheckerTest extends PHPStanTestCase
{

	private PrototypeServiceDefinitionChecker $subject;

	public static function provideNonPrototypes(): \Generator
	{
		$builderFactory = new BuilderFactory();
		// stdClass has no required constructor arguments (it's a prototype candidate)
		$prototypeClass = $builderFactory->classConstFetch(\stdClass::class, 'class');
		// Site has required constructor arguments (not a prototype candidate)
		$nonPrototypeClass = $builderFactory->classConstFetch(Site::class, 'class');

		yield 'Service definition has tags' => [
			$builderFactory->staticCall('Foo', 'foo', [$prototypeClass]),
			new ServiceDefinition('foo', 'bar', false, false, null, false, false, true),
		];

		yield 'Service definition has method calls' => [
			$builderFactory->staticCall('Foo', 'foo', [$prototypeClass]),
			new ServiceDefinition('foo', 'bar', false, false, null, false, true, false),
		];

		yield 'Service definition has non prototype class' => [
			$builderFactory->staticCall('Foo', 'foo', [$nonPrototypeClass]),
			new ServiceDefinition('foo', 'bar', false, false, null, false, false, false),
		];
	}

	public static function providePrototypes(): \Generator
	{
		$builderFactory = new BuilderFactory();
		// stdClass - a built-in PHP class with no constructor that PHPStan knows about
		$prototypeClass = $builderFactory->classConstFetch(\stdClass::class, 'class');
		// NullSite - a TYPO3 class with no required constructor arguments
		$prototypeClassWithoutConstructor = $builderFactory->classConstFetch(NullSite::class, 'class');

		yield 'Service definition has no tags, no method calls and class has no required constructor arguments' => [
			$builderFactory->staticCall('Foo', 'foo', [$prototypeClass]),
			new ServiceDefinition('foo', 'bar', false, false, null, false, false, false),
		];
		yield 'Service definition has no tags, no method calls and class has no constructor at all' => [
			$builderFactory->staticCall('Foo', 'foo', [$prototypeClassWithoutConstructor]),
			new ServiceDefinition('foo', 'bar', false, false, null, false, false, false),
		];
	}

	protected function setUp(): void
	{
		$this->subject = self::getContainer()->getByType(PrototypeServiceDefinitionChecker::class);
	}

	/**
	 * @dataProvider providePrototypes
	 */
	public function testIsPrototypeIsTrue(StaticCall $node, ServiceDefinition $serviceDefinition): void
	{
		self::assertTrue($this->subject->isPrototype($serviceDefinition, $node));
	}

	/**
	 * @dataProvider provideNonPrototypes
	 */
	public function testIsPrototypeIsFalse(StaticCall $node, ServiceDefinition $serviceDefinition): void
	{
		self::assertFalse($this->subject->isPrototype($serviceDefinition, $node));
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../../extension.neon',
		];
	}

}
