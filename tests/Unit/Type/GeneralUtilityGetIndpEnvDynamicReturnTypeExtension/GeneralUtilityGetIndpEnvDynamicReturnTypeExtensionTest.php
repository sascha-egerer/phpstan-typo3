<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\GeneralUtilityGetIndpEnvDynamicReturnTypeExtension;

use PHPStan\Testing\TypeInferenceTestCase;

final class GeneralUtilityGetIndpEnvDynamicReturnTypeExtensionTest extends TypeInferenceTestCase
{

	/**
	 * @return iterable<mixed>
	 */
	public function dataFileAsserts(): iterable
	{
		yield from $this->gatherAssertTypes(__DIR__ . '/data/GeneralUtilityGetIndpEnvTest.php');
	}

	/**
	 * @dataProvider dataFileAsserts
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
			__DIR__ . '/../../../../extension.neon',
		];
	}

}
