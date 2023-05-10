<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Type;

use PHPStan\Testing\TypeInferenceTestCase;

class SiteGetAttributeDynamicReturnTypeExtensionTest extends TypeInferenceTestCase
{

	/**
	 * @return iterable<mixed>
	 */
	public function dataFileAsserts(): iterable
	{
		// path to a file with actual asserts of expected types:
		yield from $this->gatherAssertTypes(__DIR__ . '/data/site-get-attribute-return-types.php');
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
		mixed ...$args
	): void
	{
		$this->assertFileAsserts($assertType, $file, ...$args);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [
			__DIR__ . '/../../../extension.neon',
			__DIR__ . '/data/site-get-attribute-return-types.neon',
		];
	}

}
