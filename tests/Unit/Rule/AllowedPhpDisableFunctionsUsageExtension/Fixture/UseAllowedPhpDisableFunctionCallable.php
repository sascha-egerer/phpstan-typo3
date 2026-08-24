<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\AllowedPhpDisableFunctionsUsageExtension\Fixture;

final class UseAllowedPhpDisableFunctionCallable
{
    public function someMethod(): void
    {
        $setTimeLimit = set_time_limit(...);
        $putenv = \putenv(...);
        $strlen = strlen(...);
        $setTimeLimit(30);
        $putenv('FOO=bar');
        $strlen('foo');
    }

}
