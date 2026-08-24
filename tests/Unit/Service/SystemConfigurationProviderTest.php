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

    public function testThatAnExceptionIsThrownWhenFileIsNotReadable(): void
    {
        $settingsFile = tempnam(sys_get_temp_dir(), 'phpstan-typo3-settings-');
        self::assertIsString($settingsFile);
        file_put_contents($settingsFile, '<?php return [];');
        chmod($settingsFile, 0o000);

        if (is_readable($settingsFile)) {
            unlink($settingsFile);
            self::markTestSkipped('File is still readable, probably running as root');
        }

        try {
            $this->expectException(SystemSettingsFileException::class);

            (new SystemConfigurationProvider($settingsFile))->getAllowedPhpDisableFunctions();
        } finally {
            unlink($settingsFile);
        }
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
