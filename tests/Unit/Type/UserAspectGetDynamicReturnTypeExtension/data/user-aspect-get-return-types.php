<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Type\UserAspectGetDynamicReturnTypeExtension\data;

use TYPO3\CMS\Core\Context\UserAspect;

use function PHPStan\Testing\assertType;

class MyContext
{
    public function getTests(UserAspect $context): void
    {
        assertType('int<0, max>', $context->get('id'));
        assertType('string', $context->get('username'));
        assertType('bool', $context->get('isLoggedIn'));
        assertType('bool', $context->get('isAdmin'));
        assertType('array<int, int<-2, max>>', $context->get('groupIds'));
        assertType('array<int, string>', $context->get('groupNames'));
    }

}
