<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\RequestAttributeValidationRule\Fixture;

use Psr\Http\Message\ServerRequestInterface;

final class UseUndefinedRequestAttribute
{
    public function someMethod(): void
    {
        $this->getServerRequest()->getAttribute('foo');
    }

    public function getServerRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }

}
