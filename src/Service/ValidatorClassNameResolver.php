<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Service;

use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\Type;
use ReflectionMethod;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;

final class ValidatorClassNameResolver
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
	}

	public function resolve(Type $type): ?string
	{
		if ( ! $type instanceof ConstantStringType) {
			return null;
		}

		if ($this->reflectionProvider->hasClass(\TYPO3\CMS\Extbase\Validation\ValidatorClassNameResolver::class)) {
			return \TYPO3\CMS\Extbase\Validation\ValidatorClassNameResolver::resolve($type->getValue());
		}

		if ( ! $this->reflectionProvider->hasClass(ValidatorResolver::class)) {
			return null;
		}

		// This is for older TYPO3 Versions where the class ValidatorClassNameResolver does not exist yet =< 9.5
		try {
			$reflectionMethod = new ReflectionMethod(ValidatorResolver::class, 'resolveValidatorObjectName');
			$reflectionMethod->setAccessible(true);

			return $reflectionMethod->invokeArgs(new ValidatorResolver(), [$type->getValue()]);
		} catch (\ReflectionException $exception) {
			return null;
		}
	}

}
