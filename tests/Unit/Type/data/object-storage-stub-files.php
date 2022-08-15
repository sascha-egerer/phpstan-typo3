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

	/** @var ObjectStorage<MyModel> */
	protected ObjectStorage $objectStorage;

	public function foo(): void
	{
		$myModel = new MyModel();
		$this->objectStorage = new ObjectStorage();
		$this->objectStorage->attach($myModel);

		assertType('TYPO3\CMS\Extbase\Persistence\ObjectStorage<' . self::class . '>', $this->objectStorage);

		foreach ($this->objectStorage as $key => $value) {
			assertType('int', $key);
			assertType(self::class, $value);
		}

		assertType(self::class . '|null', $this->objectStorage->offsetGet(0));

		// We ignore errors in the next line as this will produce an
		// "Offset 0 does not exist on TYPO3\CMS\Extbase\Persistence\ObjectStorage<ObjectStorage\My\Test\Extension\Domain\Model\MyModel>
		// due to the weird implementation of ArrayAccess in ObjectStorage::offsetGet()
		// @phpstan-ignore-next-line
		assertType(self::class . '|null', $this->objectStorage[0]);

		assertType('mixed', $this->objectStorage->offsetGet($myModel));
	}

}
