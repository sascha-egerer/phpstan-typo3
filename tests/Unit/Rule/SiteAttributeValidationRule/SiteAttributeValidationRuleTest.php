<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\SiteAttributeValidationRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use SaschaEgerer\PhpstanTypo3\Rule\SiteAttributeValidationRule;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @extends RuleTestCase<SiteAttributeValidationRule>
 */
final class SiteAttributeValidationRuleTest extends RuleTestCase
{
    public function testRuleWithoutErrors(): void
    {
        $this->analyse([__DIR__ . '/Fixture/UseSiteWithDefinedAttribute.php'], []);
    }

    public function testRuleWithErrors(): void
    {
        $this->analyse(
            [__DIR__ . '/Fixture/UseSiteWithUndefinedAttribute.php'],
            [
                [
                    'There is no site attribute "foo" configured so we can\'t figure out the exact type to return when calling ' . Site::class . '::getAttribute',
                    20,
                    'You should add custom site attribute to the typo3.siteGetAttributeMapping setting.',
                ],
            ]
        );
    }

    protected function getRule(): Rule
    {
        return new SiteAttributeValidationRule([
            'languages' => 'de',
        ]);
    }

}
