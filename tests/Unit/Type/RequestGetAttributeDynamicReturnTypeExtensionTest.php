<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Type;

use PHPStan\Testing\TypeInferenceTestCase;

class RequestGetAttributeDynamicReturnTypeExtensionTest extends TypeInferenceTestCase
{

	/**
	 * @return iterable<mixed>
	 */
	public static function dataFileAsserts(): iterable
	{
		// path to a file with actual asserts of expected types:
		yield from self::gatherAssertTypes(__DIR__ . '/data/request-get-attribute-return-types.php');
	}

	/**
	 * @dataProvider dataFileAsserts
	 *
	 * @param string $assertType
	 * @param string $file
	 * @param mixed ...$args
	 */
	public function testFileAsserts(
		string $assertType,
		string $file,
		...$args
	): void
	{
		$this->assertFileAsserts($assertType, $file, ...$args);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../../extension.neon',
			__DIR__ . '/data/request-get-attribute-return-types.neon',
		];
	}

}
