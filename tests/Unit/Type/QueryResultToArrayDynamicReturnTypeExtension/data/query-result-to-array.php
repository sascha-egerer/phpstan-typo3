<?php

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\QueryResultToArrayDynamicReturnTypeExtension;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Repository;
use function PHPStan\Testing\assertType;

/**
 * @extends Repository<FrontendUserGroup>
 */
class FrontendUserGroupRepository extends Repository
{

}

class FrontendUserGroup extends AbstractEntity
{
}

class MyController extends ActionController
{
	/**
	 * @var FrontendUserGroupRepository
	 */
	private $myRepository;

	public function __construct(FrontendUserGroupRepository $myRepository)
	{
		$this->myRepository = $myRepository;
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
	}
}
