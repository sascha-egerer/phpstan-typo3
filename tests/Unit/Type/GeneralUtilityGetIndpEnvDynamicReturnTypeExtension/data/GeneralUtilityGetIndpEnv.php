<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\GeneralUtilityGetIndpEnvDynamicReturnTypeExtension\data;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use function PHPStan\Testing\assertType;

final class GeneralUtilityGetIndpEnv
{

	public function getScriptName(): void
	{
		$value = GeneralUtility::getIndpEnv('SCRIPT_NAME');
		assertType('string', $value);
	}

	public function getSsl(): void
	{
		$value = GeneralUtility::getIndpEnv('TYPO3_SSL');
		assertType('bool', $value);
	}

	public function getTypo3Proxy(): void
	{
		$value = GeneralUtility::getIndpEnv('TYPO3_PROXY');
		assertType('bool', $value);
	}

	public function getArray(): void
	{
		$value = GeneralUtility::getIndpEnv('_ARRAY');
		assertType('array<string, bool|string>', $value);
	}

}
