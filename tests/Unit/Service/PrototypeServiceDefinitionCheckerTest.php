<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Service;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Testing\PHPStanTestCase;
use SaschaEgerer\PhpstanTypo3\Service\PrototypeServiceDefinitionChecker;
use SaschaEgerer\PhpstanTypo3\Service\ServiceDefinition;
use SaschaEgerer\PhpstanTypo3\Tests\Unit\Fixtures\NonPrototypeClass;
use SaschaEgerer\PhpstanTypo3\Tests\Unit\Fixtures\PrototypeClass;
use SaschaEgerer\PhpstanTypo3\Tests\Unit\Fixtures\PrototypeClassWithoutConstructor;

final class PrototypeServiceDefinitionCheckerTest extends PHPStanTestCase
{

	private PrototypeServiceDefinitionChecker $subject;

	public static function provideNonPrototypes(): \Generator
	{
		$builderFactory = new BuilderFactory();
		$prototypeClass = $builderFactory->classConstFetch(PrototypeClass::class, 'class');
		$nonPrototypeClass = $builderFactory->classConstFetch(NonPrototypeClass::class, 'class');

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
		$prototypeClass = $builderFactory->classConstFetch(self::class, 'class');
		$prototypeClassWithoutConstructor = $builderFactory->classConstFetch(PrototypeClassWithoutConstructor::class, 'class');

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
