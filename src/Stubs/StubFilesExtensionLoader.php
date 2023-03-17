<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Stubs;

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;

class StubFilesExtensionLoader implements \PHPStan\PhpDoc\StubFilesExtension
{

	public function getFiles(): array
	{
		$stubsDir = dirname(__DIR__, 2) . '/stubs';
		$files = [];
		if (InstalledVersions::satisfies(new VersionParser(), 'typo3/cms-core', '< 12')) {
			$files[] = $stubsDir . '/GeneralUtility.stub';
		}
		if (InstalledVersions::satisfies(new VersionParser(), 'typo3/cms-core', '<= 12.2.0')) {
			$files[] = $stubsDir . '/QueryResult.stub';
		}

		return $files;
	}

}
