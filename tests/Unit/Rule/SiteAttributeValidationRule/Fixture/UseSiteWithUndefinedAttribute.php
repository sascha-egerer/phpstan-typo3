<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\SiteAttributeValidationRule\Fixture;

use TYPO3\CMS\Core\Site\Entity\Site;

final class UseSiteWithUndefinedAttribute
{

	private Site $site;

	public function __construct(Site $site)
	{
		$this->site = $site;
	}

	public function someMethod(): void
	{
		$this->site->getAttribute('foo');
	}

}
