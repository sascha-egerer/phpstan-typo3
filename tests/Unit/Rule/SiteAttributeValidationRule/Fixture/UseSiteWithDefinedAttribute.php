<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\SiteAttributeValidationRule\Fixture;

use TYPO3\CMS\Core\Site\Entity\Site;

final class UseSiteWithDefinedAttribute
{

	/** @var Site */
	private $site;

	public function __construct(Site $site)
	{
		$this->site = $site;
	}

	public function someMethod(): void
	{
		$this->site->getAttribute('languages');

		$this->site->getLanguageById(1);

		$classWithGetAttributeMethod = new class {

			public function getAttribute(string $attributeName): string
			{
				return 'foo';
			}

		};

		$classWithGetAttributeMethod->getAttribute('foo');

		$this->site->getAttribute();
	}

}
