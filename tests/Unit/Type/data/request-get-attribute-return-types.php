<?php declare(strict_types = 1);

namespace RequestGetAttributeReturnTypes;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use function PHPStan\Testing\assertType;

// phpcs:ignore Squiz.Classes.ClassFileName.NoMatch
class MyRequest
{

	public function getAttributeTests(ServerRequestInterface $request): void
	{
		assertType(SiteLanguage::class, $request->getAttribute('language'));
		assertType(Site::class, $request->getAttribute('site'));
		assertType(NormalizedParams::class, $request->getAttribute('normalizedParams'));
		assertType('1|2|4|8|16', $request->getAttribute('applicationType'));
		assertType('FlowdGmbh\\MyProject\\Http\\MyAttribute', $request->getAttribute('myCustomAttribute'));
		assertType('FlowdGmbh\\MyProject\\Http\\MyAttribute|null', $request->getAttribute('myCustomNullableAttribute'));
	}

}
