<?php

declare(strict_types=1);

namespace RequestGetAttributeReturnTypes;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

use function PHPStan\Testing\assertType;

class MyRequest
{
    public function getAttributeTests(ServerRequestInterface $request): void
    {
        assertType(
            \TYPO3\CMS\Core\Routing\PageArguments::class . '|' . \TYPO3\CMS\Core\Routing\SiteRouteResult::class
            . '|null',
            $request->getAttribute('routing')
        );
        assertType(SiteLanguage::class . '|null', $request->getAttribute('language'));
        assertType(Site::class . '|null', $request->getAttribute('site'));
        assertType(NullSite::class . '|' . Site::class, $request->getAttribute('site', new NullSite()));
        assertType(NormalizedParams::class . '|null', $request->getAttribute('normalizedParams'));
        assertType('1|2|4|8|16|null', $request->getAttribute('applicationType'));
        assertType('FlowdGmbh\\MyProject\\Http\\MyAttribute|null', $request->getAttribute('myCustomAttribute'));
        assertType(
            'FlowdGmbh\\MyProject\\Http\\MyAttribute|int|null',
            $request->getAttribute('myCustomThatCanBeIntAttribute')
        );
    }

}
