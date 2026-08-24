<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Rule\AllowedPhpDisableFunctionsUsageExtension\Fixture;

final class UseAllowedPhpDisableFunction
{
    public function someMethod(): void
    {
        set_time_limit(30);
        \putenv('FOO=bar');
        phpinfo();
        strlen('foo');
    }

}
