<?php declare(strict_types = 1);

// phpcs:disable SlevomatCodingStandard.Namespaces.RequireOneNamespaceInFile.MoreNamespacesInFile
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses

namespace RepositoryStubFiles\My\Test\Extension\Domain\Model;

class MyModel extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

	/** @var string */
	protected $foo;

}

namespace RepositoryStubFiles\My\Test\Extension\Domain\Repository;

use function PHPStan\Testing\assertType;

	/** @extends \TYPO3\CMS\Extbase\Persistence\Repository<\RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModel> */
class MyModelRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

	public function myTests(): void
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
			$this->findByFoo('a')
		);

		assertType(
			'RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModel|null',
			$this->findOneByFoo('a')
		);

		assertType(
			'array<int, RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModel>',
			$this->findByFoo('a')->toArray()
		);

		assertType(
			'int',
			$this->countByFoo('a')
		);
	}

}

class MyModelWithoutExtends extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

}

/** @phpstan-ignore-next-line */
class MyModelWithoutExtendsRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

	public function myTests(): void
	{
		assertType(
			'TYPO3\CMS\Extbase\Persistence\QueryResultInterface<RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModelWithoutExtends>',
			$this->findAll()
		);
	}

}

/** @extends \TYPO3\CMS\Extbase\Persistence\Repository<\RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModel> */
class FindAllTestRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

	public function myTests(): void
	{
		assertType(
			'array<int, RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModel>',
			$this->findAll()
		);
	}

	/**
	 * @return list<\RepositoryStubFiles\My\Test\Extension\Domain\Model\MyModel>
	 */
	public function findAll(): array
	{
		return [];
	}

}
