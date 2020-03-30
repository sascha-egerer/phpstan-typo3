<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Reflection;

use PHPStan\Broker\Broker;
use PHPStan\Reflection\BrokerAwareExtension;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use TYPO3\CMS\Extbase\Persistence\Repository;

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
			!$classReflection->hasNativeMethod($methodName)
			&& in_array(Repository::class, $classReflection->getParentClassesNames(), true)
		) {
			return strpos($methodName, 'findBy') === 0 || strpos($methodName, 'findOneBy') === 0;
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

}
