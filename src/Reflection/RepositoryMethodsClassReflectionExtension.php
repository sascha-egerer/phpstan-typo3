<?php
declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Reflection;

use PHPStan\Broker\Broker;
use PHPStan\Reflection\BrokerAwareClassReflectionExtension;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;

class RepositoryMethodsClassReflectionExtension implements \PHPStan\Reflection\MethodsClassReflectionExtension, BrokerAwareClassReflectionExtension
{
    /**
     * @var Broker
     */
    private $broker;

    public function setBroker(Broker $broker)
    {
        $this->broker = $broker;
    }

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if (
            !$classReflection->getNativeReflection()->hasMethod($methodName)
            && $classReflection->isSubclassOf(\TYPO3\CMS\Extbase\Persistence\Repository::class)
        ) {
            return 0 === strpos($methodName, 'findBy') || 0 === strpos($methodName, 'findOneBy');
        }
        return false;
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        if (0 === strpos($methodName, 'findOneBy')) {
            $methodReflection = new RepositoryFindOneByMethodReflection($classReflection, $methodName, $this->broker);
        } else {
            $methodReflection = new RepositoryFindByMethodReflection($classReflection, $methodName);
        }

        return $methodReflection;
    }
}
