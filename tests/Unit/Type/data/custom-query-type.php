<?php
declare(strict_types=1);

namespace My\Test\Extension\Domain\Model {
	use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

	// Extbase naming convention says this model should be called MyModel, but we want to test that it also works if we
	// have a different model name
	class SomeOtherModel extends AbstractEntity {}
}

namespace My\Test\Extension\Domain\Repository {

	use My\Test\Extension\Domain\Model\SomeOtherModel;
	use TYPO3\CMS\Extbase\Persistence\QueryInterface;
	use function PHPStan\Testing\assertType;

	class MyModelRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
		public function findBySomething()
		{
			/** @var QueryInterface<SomeOtherModel> $query */
			$query = $this->persistenceManager->createQueryForType(SomeOtherModel::class);

			$result = $query->execute();
			assertType('TYPO3\CMS\Extbase\Persistence\QueryInterface<My\Test\Extension\Domain\Model\SomeOtherModel>', $query);

			$rawResult = $query->execute(true);
			assertType('array<int, My\Test\Extension\Domain\Model\SomeOtherModel>', $rawResult);
		}
	}
}
