<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\RequestAttributeValidationRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Psr\Http\Message\ServerRequestInterface;
use SaschaEgerer\PhpstanTypo3\Rule\RequestAttributeValidationRule;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * @extends RuleTestCase<RequestAttributeValidationRule>
 */
final class RequestAttributeValidationRuleTest extends RuleTestCase
{
    public function testRuleWithErrors(): void
    {
        $this->analyse(
            [__DIR__ . '/Fixture/UseUndefinedRequestAttribute.php'],
            [
                [
                    'There is no request attribute "foo" configured so we can\'t figure out the exact type to return when calling ' . ServerRequestInterface::class . '::getAttribute',
                    13,
                    'You should add custom request attribute to the typo3.requestGetAttributeMapping setting.',
                ],
            ]
        );
    }

    public function testRuleWithoutErrors(): void
    {
        $this->analyse([__DIR__ . '/Fixture/UseDefinedRequestAttribute.php'], []);
    }

    protected function getRule(): Rule
    {
        return new RequestAttributeValidationRule([
            'backend.user' => BackendUserAuthentication::class,
        ]);
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/../../../../extension.neon'];
    }

}
