<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Reflection;

use PHPStan\Broker\Broker;
use PHPStan\Reflection\BrokerAwareExtension;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;

class RepositoryFindMethodsClassReflectionExtension implements MethodsClassReflectionExtension, BrokerAwareExtension
{

	/** @var Broker */
	private $broker;

	public function setBroker(Broker $broker): void
	{
		$this->broker = $broker;
	}

	public function hasMethod(ClassReflection $classReflection, string $methodName): bool
	{
		if (!in_array(Repository::class, $classReflection->getParentClassesNames(), true)) {
			return false;
		}

		if ($classReflection->hasNativeMethod($methodName)) {
			return false;
		}

		if (strpos($methodName, 'findOneBy') === 0) {
			$propertyName = lcfirst(substr($methodName, 9));
		} elseif (strpos($methodName, 'findBy') === 0) {
			$propertyName = lcfirst(substr($methodName, 6));
		} else {
			return false;
		}

		// ensure that a property with that name exists on the model, as there might
		// be methods starting with find[One]By... with custom implementations on
		// inherited repositories
		$className = $classReflection->getName();
		$modelName = ClassNamingUtility::translateRepositoryNameToModelName($className);

		$modelReflection = $this->broker->getClass($modelName);
		if (!$modelReflection->hasProperty($propertyName)) {
			return false;
		}

		return true;
	}

	public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
	{
		if (strpos($methodName, 'findOneBy') === 0) {
			$methodReflection = new RepositoryFindOneByMethodReflection($classReflection, $methodName, $this->broker);
		} else {
			$methodReflection = new RepositoryFindByMethodReflection($classReflection, $methodName, $this->broker);
		}

		return $methodReflection;
	}

}
