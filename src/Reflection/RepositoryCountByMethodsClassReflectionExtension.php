<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Reflection;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\ReflectionProvider;
use SaschaEgerer\PhpstanTypo3\Helpers\Typo3ClassNamingUtilityTrait;
use TYPO3\CMS\Extbase\Persistence\Repository;

class RepositoryCountByMethodsClassReflectionExtension implements MethodsClassReflectionExtension
{

	use Typo3ClassNamingUtilityTrait;

	private ReflectionProvider $reflectionProvider;

	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
	}

	public function hasMethod(ClassReflection $classReflection, string $methodName): bool
	{
		if (!in_array(Repository::class, $classReflection->getParentClassesNames(), true)) {
			return false;
		}

		if ($classReflection->hasNativeMethod($methodName)) {
			return false;
		}

		if ($classReflection->isAbstract()) {
			return false;
		}

		if (strpos($methodName, 'countBy') !== 0) {
			return false;
		}

		$propertyName = lcfirst(substr($methodName, strlen('countBy')));

		// ensure that a property with that name exists on the model, as there might
		// be methods starting with find[One]By... with custom implementations on
		// inherited repositories
		$className = $classReflection->getName();
		$modelName = $this->translateRepositoryNameToModelName($className);

		$modelReflection = $this->reflectionProvider->getClass($modelName);
		return $modelReflection->hasProperty($propertyName);
	}

	public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
	{
		return new RepositoryCountByMethodReflection($classReflection, $methodName);
	}

}
