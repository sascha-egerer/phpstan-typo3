<?php
declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Service;

final class ServiceDefinitionFileException extends \InvalidArgumentException
{
	public static function notFound(string $file): self
	{
		$message = sprintf('File "%s" does not exist', $file);

		return new self($message);
	}

	public static function parseError(string $file): self
	{
		$message = sprintf('File "%s" could not be parsed correctly', $file);

		return new self($message);
	}
}
