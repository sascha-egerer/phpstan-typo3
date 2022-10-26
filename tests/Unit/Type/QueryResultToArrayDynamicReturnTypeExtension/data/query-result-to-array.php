<?php declare(strict_types = 1);

// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\QueryResultToArrayDynamicReturnTypeExtension;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use function PHPStan\Testing\assertType;

/**
 * @extends Repository<FrontendUserGroup>
 */
class FrontendUserGroupRepository extends Repository
{

}

/**
 * @extends Repository<FrontendUserGroup>
 */
class FrontendUserCustomFindAllGroupRepository extends Repository
{

	/**
	 * @return QueryResultInterface<FrontendUserGroup>
	 */
	public function findAll(): QueryResultInterface // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingAnyTypeHint
	{
		$queryResult = null; // phpcs:ignore SlevomatCodingStandard.Variables.UselessVariable.UselessVariable
		/** @var QueryResult<FrontendUserGroup> $queryResult */
		return $queryResult;
	}

}

class FrontendUserGroup extends AbstractEntity
{

}

class MyController extends ActionController
{

	/** @var FrontendUserGroupRepository */
	private $myRepository;

	/** @var FrontendUserCustomFindAllGroupRepository */
	private $myCustomFindAllRepository;

	public function __construct(
		FrontendUserGroupRepository $myRepository,
		FrontendUserCustomFindAllGroupRepository $myCustomFindAllRepository
	)
	{
		$this->myRepository = $myRepository;
		$this->myCustomFindAllRepository = $myCustomFindAllRepository;
	}

	public function showAction(): void
	{
		assertType(
			'array<int, SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\QueryResultToArrayDynamicReturnTypeExtension\FrontendUserGroup>',
			$this->myRepository->findAll()->toArray()
		);

		$queryResult = $this->myRepository->findAll();
		$myObjects = $queryResult->toArray();
		assertType(
			'array<int, SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\QueryResultToArrayDynamicReturnTypeExtension\FrontendUserGroup>',
			$myObjects
		);

		assertType(
			'array<int, SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\QueryResultToArrayDynamicReturnTypeExtension\FrontendUserGroup>',
			$this->myCustomFindAllRepository->findAll()->toArray()
		);

		$queryResult = $this->myCustomFindAllRepository->findAll();
		$myObjects = $queryResult->toArray();
		assertType(
			'array<int, SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\QueryResultToArrayDynamicReturnTypeExtension\FrontendUserGroup>',
			$myObjects
		);
	}

}
