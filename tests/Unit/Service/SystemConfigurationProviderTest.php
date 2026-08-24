<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use SaschaEgerer\PhpstanTypo3\Service\SystemConfigurationProvider;
use SaschaEgerer\PhpstanTypo3\Service\SystemSettingsFileException;

final class SystemConfigurationProviderTest extends TestCase
{
    public function testThatAllowedPhpDisableFunctionsAreEmptyWhenNoSystemSettingsFileExists(): void
    {
        $provider = new SystemConfigurationProvider(null);

        self::assertSame([], $provider->getAllowedPhpDisableFunctions());
    }

    public function testThatAnExceptionIsThrownWhenFileDoesNotExist(): void
    {
        $this->expectException(SystemSettingsFileException::class);

        (new SystemConfigurationProvider(__DIR__ . '/foo.php'))->getAllowedPhpDisableFunctions();
    }

    public function testThatAnExceptionIsThrownWhenFileDoesNotReturnAConfigurationArray(): void
    {
        $this->expectException(SystemSettingsFileException::class);

        (new SystemConfigurationProvider(__DIR__ . '/../Fixtures/settings_with_errors.php'))->getAllowedPhpDisableFunctions();
    }

    public function testThatAllowedPhpDisableFunctionsAreReadFromSystemSettingsFile(): void
    {
        $provider = new SystemConfigurationProvider(__DIR__ . '/../Fixtures/settings.php');

        self::assertSame(['set_time_limit', 'putenv', 'PHPINFO'], $provider->getAllowedPhpDisableFunctions());
    }

}
