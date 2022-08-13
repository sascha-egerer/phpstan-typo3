<?php declare(strict_types = 1);

/**
 * We can't use SaschaEgerer\PhpstanTypo3\Trait as namespace
 * as keywords like "Trait" are not allowed in namespaces until PHP 8.0
 */

namespace SaschaEgerer\PhpstanTypo3\Helpers;

use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Extbase\Persistence\RepositoryInterface;

trait Typo3ClassNamingUtilityTrait
{

	/**
	 * @param class-string $repositoryClassName
	 * @return class-string
	 */
	protected function translateRepositoryNameToModelName(string $repositoryClassName): string
	{
		if (is_a(RepositoryInterface::class, $repositoryClassName, true)) {
			throw new \PHPStan\ShouldNotHappenException('Repository class must implement RepositoryInterface');
		}
		/** @var class-string<RepositoryInterface> $repositoryClassName */

		/** @var class-string $modelName */
		$modelName = ClassNamingUtility::translateRepositoryNameToModelName($repositoryClassName);
		return $modelName;
	}

}
