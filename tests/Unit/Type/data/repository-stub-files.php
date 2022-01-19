<?php

namespace RepositoryStubFiles\My\Test\Extension\Domain\Model {
	class MyModel extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {}
}

namespace RepositoryStubFiles\My\Test\Extension\Domain\Repository {
	use function PHPStan\Testing\assertType;

	/**
	 * @extends \TYPO3\CMS\Extbase\Persistence\Repository<\RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModel>
	 */
	class MyModelRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
	{
		public function __construct()
		{
			$model = new \RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModel();
			$this->add($model);
			assertType('class-string<static(RepositoryStubFiles\My\Test\Extension\Domain\Repository\MyModelRepository)>', $this->getRepositoryClassName());
		}
	}
}
