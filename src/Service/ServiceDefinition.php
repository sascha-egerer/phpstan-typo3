<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Service;

final readonly class ServiceDefinition
{

	public function __construct(private string $id, private ?string $class, private bool $public, private bool $synthetic, private ?string $alias, private bool $hasConstructorArguments, private bool $hasMethodCalls, private bool $hasTags)
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
