<?php
declare(strict_types=1);


namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\ContextAspectValidationRule\Fixture;


use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class UseContextApiWithDefinedAspect
{
	public function someMethod(): void
	{
		$dateAspect = GeneralUtility::makeInstance(Context::class)->getAspect('date');
		$dateAspect->get('bar');

		$class = new class {
			public function getAspect(string $name): \stdClass
			{
				return new \stdClass();
			}
		};

		$myCustomAspect = $class->getAspect('foo');
		$myCustomAspect->something = 'FooBarBaz';

		$aspectWithoutDefiningName = GeneralUtility::makeInstance(Context::class)->getAspect();
		$aspectWithoutDefiningName->get('bar');

		GeneralUtility::makeInstance(Context::class)->setAspect('dates', new DateTimeAspect(new \DateTimeImmutable()));

		GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('dates', 'foo');
	}
}
