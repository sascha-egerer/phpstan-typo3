<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\DateTimeAspectGetDynamicReturnTypeExtension\data;

use TYPO3\CMS\Core\Context\DateTimeAspect;

use function PHPStan\Testing\assertType;

// phpcs:ignore Squiz.Classes.ClassFileName.NoMatch
class MyContext
{

	public function getTests(DateTimeAspect $context): void
	{
		assertType('int', $context->get('timestamp'));
		assertType('string', $context->get('iso'));
		assertType('string', $context->get('timezone'));
		assertType(\DateTimeImmutable::class, $context->get('full'));
		assertType('int', $context->get('accessTime'));
	}

}
