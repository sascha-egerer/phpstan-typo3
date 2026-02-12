<?php

declare(strict_types=1);

// phpcs:disable SlevomatCodingStandard.Namespaces.RequireOneNamespaceInFile.MoreNamespacesInFile
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses

namespace CustomQueryType\My\Test\Extension\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

// Extbase naming convention says this model should be called MyModel, but we want to test that it also works if we
// have a different model name
class SomeOtherModel extends AbstractEntity {}

namespace CustomQueryType\My\Test\Extension\Domain\Repository;

use CustomQueryType\My\Test\Extension\Domain\Model\SomeOtherModel;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

use function PHPStan\Testing\assertType;

/**
 * @extends Repository<SomeOtherModel>
 */
class MyModelRepository extends Repository
{
    public function findBySomething(bool $booleanParameter = false): void
    {
        /** @var QueryInterface<SomeOtherModel> $query */
        $query = $this->persistenceManager->createQueryForType(SomeOtherModel::class);

        $result = $query->execute();
        assertType(
            'TYPO3\CMS\Extbase\Persistence\QueryResultInterface<int, CustomQueryType\My\Test\Extension\Domain\Model\SomeOtherModel>',
            $result
        );

        $result = $query->execute(false);
        assertType(
            'TYPO3\CMS\Extbase\Persistence\QueryResultInterface<int, CustomQueryType\My\Test\Extension\Domain\Model\SomeOtherModel>',
            $result
        );

        $result = $query->execute($booleanParameter);
        assertType(
            'list<array<string, mixed>>|TYPO3\CMS\Extbase\Persistence\QueryResultInterface<int, CustomQueryType\My\Test\Extension\Domain\Model\SomeOtherModel>',
            $result
        );

        $rawResult = $query->execute(true);
        assertType('list<array<string, mixed>>', $rawResult);

        $array = $result->toArray();
        assertType('list<CustomQueryType\My\Test\Extension\Domain\Model\SomeOtherModel>', $array);
    }

}
