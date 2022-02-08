<?php declare(strict_types = 1);

namespace QueryFactoryStubFile;

use TYPO3\CMS\Extbase\Persistence\Generic\QueryFactory;
use function PHPStan\Testing\assertType;

// phpcs:ignore Squiz.Classes.ClassFileName.NoMatch
class Model extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

	public function foo(): void
	{
		$createResult = $this->getQueryFactory()->create(self::class);

		assertType('TYPO3\CMS\Extbase\Persistence\QueryInterface<QueryFactoryStubFile\Model>', $createResult);
		assertType('class-string<' . self::class . '>', $createResult->getType());
	}

	private function getQueryFactory(): QueryFactory
	{
		/** @var QueryFactory $queryFactory */
		$queryFactory = null;
		return $queryFactory;
	}

}
