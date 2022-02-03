<?php

namespace RepositoryStubFiles\My\Test\Extension\Domain\Model {
	class MyModel extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
		/**
		 * @var string
		 */
		protected $foo;
	}
}

namespace RepositoryStubFiles\My\Test\Extension\Domain\Repository {
	use function PHPStan\Testing\assertType;

	/** @extends \TYPO3\CMS\Extbase\Persistence\Repository<\RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModel> */
	class MyModelRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
	{
		public function __construct()
		{
			$model = new \RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModel();
			$this->add($model);
			assertType(
				'class-string<static(RepositoryStubFiles\My\Test\Extension\Domain\Repository\MyModelRepository)>',
				$this->getRepositoryClassName()
			);

			assertType(
				'TYPO3\CMS\Extbase\Persistence\QueryResultInterface<RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModel>',
				$this->findAll()
			);

			assertType(
				'TYPO3\CMS\Extbase\Persistence\QueryResultInterface<RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModel>',
				$this->findByFoo()
			);

			assertType(
				'RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModel|null',
				$this->findOneByFoo()
			);

			assertType(
				'array<int, RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModel>',
				$this->findByFoo()->toArray()
			);

			assertType(
				'int',
				$this->countByFoo()
			);

			assertType(
				'int',
				$this->countByFoo('a')
			);
		}
	}

    class MyModelWithoutExtendsRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
    {
        public function __construct()
        {
            assertType(
                'TYPO3\CMS\Extbase\Persistence\QueryResultInterface<TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface>',
                $this->findAll()
            );
        }
    }
}
