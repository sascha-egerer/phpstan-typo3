<?php

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\QueryResultToArrayDynamicReturnTypeExtension;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\RepositoryInterface;
use function PHPStan\Testing\assertType;

/**
 * @extends Repository<FrontendUserGroup>
 */
class FrontendUserGroupRepository extends Repository implements RepositoryInterface
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

	public function showAction()
	{
		$queryResult = $this->myRepository->findAll();
		$myObjects = $queryResult->toArray();
		assertType('array<int, FrontendUserGroup>', $myObjects);
	}
}
