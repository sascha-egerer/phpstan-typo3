<?php

declare(strict_types=1);

namespace SaschaEgerer\PhpstanTypo3\Service;

use PHPStan\Type\Type;
use TYPO3\CMS\Extbase\Validation\Exception\NoSuchValidatorException;

final class ValidatorClassNameResolver
{
    /**
     * @throws NoSuchValidatorException
     */
    public function resolve(Type $type): ?string
    {
        if ($type->getConstantStrings() === []) {
            return null;
        }

        return \TYPO3\CMS\Extbase\Validation\ValidatorClassNameResolver::resolve($type->getConstantStrings()[0]->getValue());
    }

}
