<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Helpers;

use PHPStan\Type\Accessory\AccessoryLiteralStringType;
use PHPStan\Type\Accessory\AccessoryNonEmptyStringType;
use PHPStan\Type\Accessory\AccessoryNonFalsyStringType;
use PHPStan\Type\Accessory\AccessoryNumericStringType;
use PHPStan\Type\Accessory\NonEmptyArrayType;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BenevolentUnionType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerRangeType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\IterableType;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\UnionType;

class TypeNamingUtility
{

	public static function translateTypeNameToType(string $typeName): ?Type
	{
		switch (strtolower($typeName)) {
			case 'int':
			case 'integer':
				return new IntegerType();

			case 'positive-int':
				return IntegerRangeType::fromInterval(1, null);

			case 'negative-int':
				return IntegerRangeType::fromInterval(null, -1);

			case 'string':
				return new StringType();

			case 'literal-string':
				return new IntersectionType([new StringType(), new AccessoryLiteralStringType()]);

			case 'array-key':
				return new BenevolentUnionType([new IntegerType(), new StringType()]);

			case 'number':
				return new UnionType([new IntegerType(), new FloatType()]);

			case 'numeric':
				return new UnionType([
					new IntegerType(),
					new FloatType(),
					new IntersectionType([
						new StringType(),
						new AccessoryNumericStringType(),
					]),
				]);

			case 'numeric-string':
				return new IntersectionType([
					new StringType(),
					new AccessoryNumericStringType(),
				]);

			case 'non-empty-string':
				return new IntersectionType([
					new StringType(),
					new AccessoryNonEmptyStringType(),
				]);

			case 'truthy-string':
			case 'non-falsy-string':
				return new IntersectionType([
					new StringType(),
					new AccessoryNonFalsyStringType(),
				]);

			case 'bool':
				return new BooleanType();

			case 'boolean':
				return new BooleanType();

			case 'true':
				return new ConstantBooleanType(true);

			case 'false':
				return new ConstantBooleanType(false);

			case 'float':
				return new FloatType();

			case 'double':
				return new FloatType();

			case 'array':
			case 'associative-array':
				return new ArrayType(new MixedType(), new MixedType());

			case 'non-empty-array':
				return TypeCombinator::intersect(
					new ArrayType(new MixedType(), new MixedType()),
					new NonEmptyArrayType(),
				);

			case 'iterable':
				return new IterableType(new MixedType(), new MixedType());

			case 'mixed':
				return new MixedType(true);

			case 'list':
				return new ArrayType(new IntegerType(), new MixedType());

			case 'non-empty-list':
				return TypeCombinator::intersect(
					new ArrayType(new IntegerType(), new MixedType()),
					new NonEmptyArrayType(),
				);
		}

		return null;
	}

}
