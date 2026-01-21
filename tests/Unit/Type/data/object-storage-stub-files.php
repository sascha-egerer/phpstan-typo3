<?php

declare(strict_types=1);

// phpcs:disable SlevomatCodingStandard.Namespaces.RequireOneNamespaceInFile.MoreNamespacesInFile
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses

namespace ObjectStorage\My\Test\Extension\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

use function PHPStan\Testing\assertType;

class MyModel extends AbstractEntity
{
    /**
     * @var ObjectStorage<self>
     */
    protected ObjectStorage $testStorage;

    public function checkObjectStorageType(): void
    {
        $myModel = new self();
        /** @var ObjectStorage<MyModel> $objectStorage */
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($myModel);

        assertType('TYPO3\CMS\Extbase\Persistence\ObjectStorage<' . self::class . '>', $objectStorage);
    }

    public function checkIteration(): void
    {
        foreach ($this->testStorage as $key => $value) {
            assertType('string', $key);
            assertType(self::class, $value);
        }
    }

    public function checkArrayAccess(): void
    {
        assertType(self::class . '|null', $this->testStorage->offsetGet(0));
        assertType(self::class . '|null', $this->testStorage->offsetGet('0'));
        assertType(self::class . '|null', $this->testStorage->current());
        assertType(self::class . '|null', $this->testStorage[0]);

        $myModel = new self();

        assertType('mixed', $this->testStorage->offsetGet($this->testStorage->current()));
        assertType('mixed', $this->testStorage->offsetGet($myModel));
    }

}
