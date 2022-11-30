<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\MathUtilityTypeSpecifyingExtension\data;

use TYPO3\CMS\Core\Utility\MathUtility;
use function PHPStan\Testing\assertType;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingAnyTypeHint
final class MathUtilityTest
{

	public function forceIntegerInRangeWithMinAndMaxValueDefined(int $theInt): void
	{
		$forceIntegerInRange = MathUtility::forceIntegerInRange($theInt, 0, 100);
		assertType('int<0, 100>', $forceIntegerInRange);
	}

	public function forceIntegerInRangeWithoutMaxValueSpecified(int $theInt): void
	{
		$forceIntegerInRange = MathUtility::forceIntegerInRange($theInt, 0);
		assertType('int<0, 2000000000>', $forceIntegerInRange);
	}

	public function forceIntegerInRangeWithMixedType($theInt): void
	{
		$forceIntegerInRange = MathUtility::forceIntegerInRange($theInt, 4);
		assertType('int<4, 2000000000>', $forceIntegerInRange);
	}

	public function isIntegerInRange($a, string $b, float $c, int $d, int $e, int $f, int $min, int $max): void
	{
		if (MathUtility::isIntegerInRange($a, 0, 200)) {
			return;
		}

		if (MathUtility::isIntegerInRange($b, 0, 200)) {
			return;
		}

		if (MathUtility::isIntegerInRange($c, 0, 200)) {
			return;
		}

		if (MathUtility::isIntegerInRange($d, $min, $max)) {
			return;
		}

		if (MathUtility::isIntegerInRange($e, 0, $max)) {
			return;
		}

		if (MathUtility::isIntegerInRange($f, $min, 200)) {
			return;
		}

		assertType('int<0, 200>|numeric-string', $a);
		assertType('numeric-string', $b);
		assertType('*NEVER*', $c);
		assertType('int', $d);
		assertType('int<0, max>', $e);
		assertType('int<min, 200>', $f);
	}

	public function convertToPositiveInteger($a, $b = -1): void
	{
		$positiveInteger1 = MathUtility::convertToPositiveInteger($a);
		$positiveInteger2 = MathUtility::convertToPositiveInteger($b);

		assertType('int<0, max>', $positiveInteger1);
		assertType('int<0, max>', $positiveInteger2);
	}

	public function canBeInterpretedAsInteger($a, int $b, float $c, string $d): void
	{
		if (MathUtility::canBeInterpretedAsInteger($a)) {
			return;
		}

		if (MathUtility::canBeInterpretedAsInteger($b)) {
			return;
		}

		if (MathUtility::canBeInterpretedAsInteger($c)) {
			return;
		}

		if (MathUtility::canBeInterpretedAsInteger($d)) {
			return;
		}

		assertType('float|int|numeric-string', $a);
		assertType('int', $b);
		assertType('float', $c);
		assertType('numeric-string', $d);
	}

	public function canBeInterpretedAsFloat($theFloat): void
	{
		if (MathUtility::canBeInterpretedAsFloat($theFloat)) {
			return;
		}

		assertType('float', $theFloat);
	}

}
