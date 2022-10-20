<?php
declare(strict_types=1);


namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\ContextAspectValidationRule\Fixture;


use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class UseContextApiWithUndefinedAspect
{
	public function someMethod(): void
	{
		$foo = GeneralUtility::makeInstance(Context::class)->getAspect('foo');
		$foo->get('bar');
	}
}
