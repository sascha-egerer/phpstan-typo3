<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\AllowedPhpDisableFunctionsUsageExtension\Fixture;

final class UseNotDisabledPhpFunction
{
    public function someMethod(): void
    {
        strlen('foo');
        trim(' foo ');
        $trim = trim(...);
        $trim(' foo ');
    }

}
