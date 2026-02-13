<?php

declare(strict_types=1);

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
        /** @var ObjectStorage<self> $objectStorage */
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
        assertType('null', $this->testStorage->offsetGet(-2));
        assertType(self::class . '|null', $this->testStorage->offsetGet(0));
        assertType('array{obj: ' . self::class . ', inf: mixed}|' . self::class . '|null', $this->testStorage->offsetGet('0'));
        assertType(self::class . '|null', $this->testStorage->current());
        assertType(self::class . '|null', $this->testStorage[0]);

        $myModel = new self();

        assertType('array{obj: ' . self::class . ', inf: mixed}|null', $this->testStorage->offsetGet($this->testStorage->current() ?? -1));
        assertType('array{obj: ' . self::class . ', inf: mixed}|null', $this->testStorage->offsetGet($myModel));
    }
}
