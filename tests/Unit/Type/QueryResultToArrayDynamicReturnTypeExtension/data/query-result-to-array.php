<?php

declare(strict_types=1);

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
class FrontendUserGroupRepository extends Repository {}

/**
 * @extends Repository<FrontendUserGroup>
 */
class FrontendUserGroupCustomFindAllRepository extends Repository
{
    /**
     * @return QueryResultInterface<int, FrontendUserGroup>
     */
    public function findAll(): QueryResultInterface // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingAnyTypeHint
    {
        $queryResult = null; // phpcs:ignore SlevomatCodingStandard.Variables.UselessVariable.UselessVariable
        /** @var QueryResult<FrontendUserGroup> $queryResult */
        return $queryResult;
    }

}

/**
 * @extends Repository<FrontendUserGroup>
 */
class FrontendUserGroupCustomFindAllWithoutModelTypeRepository extends Repository
{
    public function findAll(): QueryResultInterface // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingAnyTypeHint
    {
        $queryResult = null; // phpcs:ignore SlevomatCodingStandard.Variables.UselessVariable.UselessVariable
        /** @var QueryResult<FrontendUserGroup> $queryResult */
        return $queryResult;
    }

}

/**
 * @extends Repository<FrontendUserGroup>
 */
class FrontendUserGroupCustomFindAllWithoutAnnotationRepository extends Repository
{
    public function findAll() // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingAnyTypeHint
    {
        $queryResult = null; // phpcs:ignore SlevomatCodingStandard.Variables.UselessVariable.UselessVariable
        /** @var QueryResult $queryResult */
        return $queryResult;
    }

}

class FrontendUserGroup extends AbstractEntity {}

class MyController extends ActionController
{
    private FrontendUserGroupRepository $myRepository;

    private FrontendUserGroupCustomFindAllRepository $myCustomFindAllRepository;

    private FrontendUserGroupCustomFindAllWithoutModelTypeRepository $myCustomFindAllRepositoryWithoutModelAnnotation;

    private FrontendUserGroupCustomFindAllWithoutAnnotationRepository $myCustomFindAllRepositoryWithoutAnnotationRepository;

    public function __construct(
        FrontendUserGroupRepository $myRepository,
        FrontendUserGroupCustomFindAllRepository $myCustomFindAllRepository,
        FrontendUserGroupCustomFindAllWithoutModelTypeRepository $myCustomFindAllRepositoryWithoutModelAnnotation,
        FrontendUserGroupCustomFindAllWithoutAnnotationRepository $myCustomFindAllRepositoryWithoutAnnotationRepository
    ) {
        $this->myRepository = $myRepository;
        $this->myCustomFindAllRepository = $myCustomFindAllRepository;
        $this->myCustomFindAllRepositoryWithoutModelAnnotation = $myCustomFindAllRepositoryWithoutModelAnnotation;
        $this->myCustomFindAllRepositoryWithoutAnnotationRepository = $myCustomFindAllRepositoryWithoutAnnotationRepository;
    }

    public function showAction(): void
    {
        assertType(
            'list<SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\QueryResultToArrayDynamicReturnTypeExtension\FrontendUserGroup>',
            $this->myRepository->findAll()->toArray()
        );

        $queryResult = $this->myRepository->findAll();
        $myObjects = $queryResult->toArray();
        assertType(
            'list<SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\QueryResultToArrayDynamicReturnTypeExtension\FrontendUserGroup>',
            $myObjects
        );

        $queryResult = $this->myCustomFindAllRepository->findAll();
        $myObjects = $queryResult->toArray();
        assertType(
            'list<SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\QueryResultToArrayDynamicReturnTypeExtension\FrontendUserGroup>',
            $myObjects
        );

        $queryResult = $this->myCustomFindAllRepositoryWithoutModelAnnotation->findAll();
        $myObjects = $queryResult->toArray();
        assertType(
            'list<SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\QueryResultToArrayDynamicReturnTypeExtension\FrontendUserGroup>',
            $myObjects
        );

        $queryResult = $this->myCustomFindAllRepositoryWithoutAnnotationRepository->findAll();
        if (is_array($queryResult)) {
            return;
        }

        $myObjects = $queryResult->toArray();
        assertType(
            'list<SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\QueryResultToArrayDynamicReturnTypeExtension\FrontendUserGroup>',
            $myObjects
        );

        $key = $queryResult->key();
        assertType(
            'int',
            $key
        );
    }

}
