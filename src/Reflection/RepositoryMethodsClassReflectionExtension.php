<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Reflection;

use PHPStan\Broker\Broker;
use PHPStan\Reflection\BrokerAwareExtension;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;

class RepositoryMethodsClassReflectionExtension implements MethodsClassReflectionExtension, BrokerAwareExtension
{

	/** @var Broker */
	private $broker;

	public function setBroker(Broker $broker): void
	{
		$this->broker = $broker;
	}

	public function hasMethod(ClassReflection $classReflection, string $methodName): bool
	{
		if (
			!$classReflection->getNativeReflection()->hasMethod($methodName)
			&& $classReflection->isSubclassOf(\TYPO3\CMS\Extbase\Persistence\Repository::class)
		) {
			// only handle magic find[One]By-methods
			if (strpos($methodName, 'findBy') !== 0 && strpos($methodName, 'findOneBy') !== 0) {
				return false;
			}

			$modelReflection = $this->broker->getClass($this->getModelName($classReflection));
			try {
				// and make sure, that the property actually exist in the model.
				// phpstan checks method existence on parent classes, for each method it finds in a child class.
				// to allow inheritance of Repositories, we need to make sure to only return true here, if that
				// method will actually resolve to a valid findBy method on this specific class.
				$modelReflection->getNativeProperty($this->getPropertyName($methodName))->getReadableType();
			} catch (\PHPStan\Reflection\MissingPropertyFromReflectionException $e) {
				return false;
			}
			return true;
		}
		return false;
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

	private function getModelName(ClassReflection $classReflection): string
	{
		$className = $classReflection->getName();
		return ClassNamingUtility::translateRepositoryNameToModelName($className);
	}

	private function getPropertyName(string $methodName): string
	{
		if (strpos($methodName, 'findBy') === 0) {
			return lcfirst(substr($methodName, 6));
		} elseif (strpos($methodName, 'findOneBy') === 0) {
			return lcfirst(substr($methodName, 9));
		}
		return $methodName;
	}

}
