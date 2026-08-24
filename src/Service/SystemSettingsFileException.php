<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Service;

final class SystemSettingsFileException extends \InvalidArgumentException
{
    public static function notFound(string $file): self
    {
        $message = sprintf('TYPO3 system settings file "%s" does not exist', $file);

        return new self($message);
    }

    public static function invalidContent(string $file): self
    {
        $message = sprintf('TYPO3 system settings file "%s" must return a configuration array', $file);

        return new self($message);
    }

}
