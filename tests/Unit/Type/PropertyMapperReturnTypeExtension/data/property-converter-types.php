<?php declare(strict_types = 1);

// phpcs:disable SlevomatCodingStandard.Namespaces.RequireOneNamespaceInFile.MoreNamespacesInFile
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\PropertyMapperReturnTypeExtension;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\File;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Property\PropertyMapper;
use function PHPStan\Testing\assertType;

class MyController extends ActionController
{

	public function convertProperties(): void
	{
		$propertyMapper = GeneralUtility::makeInstance(PropertyMapper::class);

		$frontendUser = $propertyMapper->convert(['username' => 'username'], File::class);
		assertType(File::class . '|null', $frontendUser);

		$user = $propertyMapper->convert(['username' => 'username'], 'TYPO3\CMS\Extbase\Domain\Model\File');
		assertType(File::class . '|null', $user);

		$array = $propertyMapper->convert('1,2,3', 'array');
		assertType('array|null', $array);

		$string = $propertyMapper->convert(1, 'string');
		assertType('string|null', $string);

		$integer = $propertyMapper->convert('1', 'integer');
		assertType('int|null', $integer);

		$boolean = $propertyMapper->convert('1', 'boolean');
		assertType('bool|null', $boolean);

		$mixed = $propertyMapper->convert('1', 'whatever');
		assertType('mixed', $mixed);
	}

}
