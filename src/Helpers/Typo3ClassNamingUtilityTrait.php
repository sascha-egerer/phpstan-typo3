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
		if (!is_a($repositoryClassName, RepositoryInterface::class, true)) {
			throw new \PHPStan\ShouldNotHappenException(
				sprintf('Repository class "%s" must implement "%s"', $repositoryClassName, RepositoryInterface::class)
			);
		}
		/** @var class-string $modelClass */
		$modelClass = ClassNamingUtility::translateRepositoryNameToModelName($repositoryClassName);
		return $modelClass;
	}

}
