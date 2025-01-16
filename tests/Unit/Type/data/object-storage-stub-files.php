<?php declare(strict_types = 1);

// phpcs:disable SlevomatCodingStandard.Namespaces.RequireOneNamespaceInFile.MoreNamespacesInFile
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses

namespace ObjectStorage\My\Test\Extension\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use function PHPStan\Testing\assertType;

class MyModel extends AbstractEntity
{

	public function foo(): void
	{
		$myModel = new self();
		/** @var ObjectStorage<string, MyModel> $objectStorage */
		$objectStorage = new ObjectStorage();
		$objectStorage->attach($myModel);

		assertType('TYPO3\CMS\Extbase\Persistence\ObjectStorage<string, ' . self::class . '>', $objectStorage);

		foreach ($objectStorage as $key => $value) {

			assertType('string', $key);
			assertType(self::class, $value);
		}

		assertType(self::class . '|null', $objectStorage->offsetGet(0));

		// We ignore errors in the next line as this will produce an
		// "Offset 0 does not exist on TYPO3\CMS\Extbase\Persistence\ObjectStorage<ObjectStorage\My\Test\Extension\Domain\Model\MyModel>
		// due to the weird implementation of ArrayAccess in ObjectStorage::offsetGet()
		// @phpstan-ignore-next-line
		assertType(self::class . '|null', $objectStorage[0]);

		assertType('array{obj: ObjectStorage\My\Test\Extension\Domain\Model\MyModel, inf: mixed}', $objectStorage->offsetGet($myModel));
	}

}
