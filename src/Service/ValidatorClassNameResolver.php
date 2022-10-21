<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Service;

use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\Type;

final class ValidatorClassNameResolver
{

	public function resolve(Type $type): ?string
	{
		if (!$type instanceof ConstantStringType) {
			return null;
		}

		return \TYPO3\CMS\Extbase\Validation\ValidatorClassNameResolver::resolve($type->getValue());
	}

}
