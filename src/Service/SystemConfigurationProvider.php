<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Service;

final class SystemConfigurationProvider
{
    /**
     * Default locations of the TYPO3 system settings file, relative to the
     * current working directory (composer-based and legacy installations).
     */
    private const DEFAULT_SYSTEM_SETTINGS_PATHS = [
        'config/system/settings.php',
        'typo3conf/system/settings.php',
    ];

    /** @var list<string>|null */
    private ?array $allowedPhpDisableFunctions = null;

    public function __construct(private readonly ?string $systemSettingsPath) {}

    /**
     * @return list<string>
     */
    public function getAllowedPhpDisableFunctions(): array
    {
        return $this->allowedPhpDisableFunctions ??= $this->loadAllowedPhpDisableFunctions();
    }

    /**
     * @return list<string>
     */
    private function loadAllowedPhpDisableFunctions(): array
    {
        $systemSettingsPath = $this->resolveSystemSettingsPath();

        if ($systemSettingsPath === null) {
            return [];
        }

        $systemSettings = require $systemSettingsPath;

        if (!is_array($systemSettings)) {
            throw SystemSettingsFileException::invalidContent($systemSettingsPath);
        }

        $sysConfiguration = $systemSettings['SYS'] ?? null;

        if (!is_array($sysConfiguration)) {
            return [];
        }

        $allowedPhpDisableFunctions = $sysConfiguration['allowedPhpDisableFunctions'] ?? null;

        if (!is_array($allowedPhpDisableFunctions)) {
            return [];
        }

        return array_values(array_filter($allowedPhpDisableFunctions, is_string(...)));
    }

    private function resolveSystemSettingsPath(): ?string
    {
        if ($this->systemSettingsPath !== null) {
            if (!is_file($this->systemSettingsPath)) {
                throw SystemSettingsFileException::notFound($this->systemSettingsPath);
            }

            return $this->systemSettingsPath;
        }

        foreach (self::DEFAULT_SYSTEM_SETTINGS_PATHS as $defaultSystemSettingsPath) {
            if (is_file($defaultSystemSettingsPath)) {
                return $defaultSystemSettingsPath;
            }
        }

        return null;
    }

}
