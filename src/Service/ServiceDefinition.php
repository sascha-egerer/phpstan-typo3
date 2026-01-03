<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Service;

final class ServiceDefinition
{

	public function __construct(
		private readonly string $id,
		private readonly ?string $class,
		private readonly bool $public,
		private readonly bool $synthetic,
		private readonly ?string $alias,
		private readonly bool $hasConstructorArguments,
		private readonly bool $hasMethodCalls,
		private readonly bool $hasTags
	)
	{
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getClass(): ?string
	{
		return $this->class;
	}

	public function isPublic(): bool
	{
		return $this->public;
	}

	public function isSynthetic(): bool
	{
		return $this->synthetic;
	}

	public function getAlias(): ?string
	{
		return $this->alias;
	}

	public function isHasConstructorArguments(): bool
	{
		return $this->hasConstructorArguments;
	}

	public function isHasMethodCalls(): bool
	{
		return $this->hasMethodCalls;
	}

	public function isHasTags(): bool
	{
		return $this->hasTags;
	}

}
