<?php declare(strict_types = 1);

namespace ContextGetAspectReturnTypes;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Context\TypoScriptAspect;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Context\VisibilityAspect;
use TYPO3\CMS\Core\Context\WorkspaceAspect;

use function PHPStan\Testing\assertType;

class MyContext
{

	public function getAspectTests(Context $context): void
	{
		assertType(DateTimeAspect::class, $context->getAspect('date'));
		assertType(VisibilityAspect::class, $context->getAspect('visibility'));
		assertType(UserAspect::class, $context->getAspect('backend.user'));
		assertType(UserAspect::class, $context->getAspect('frontend.user'));
		assertType(WorkspaceAspect::class, $context->getAspect('workspace'));
		assertType(LanguageAspect::class, $context->getAspect('language'));
		assertType(TypoScriptAspect::class, $context->getAspect('typoscript'));
	}

}
