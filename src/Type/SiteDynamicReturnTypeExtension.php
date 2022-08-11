<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Accessory\AccessoryLiteralStringType;
use PHPStan\Type\Accessory\AccessoryNonEmptyStringType;
use PHPStan\Type\Accessory\AccessoryNonFalsyStringType;
use PHPStan\Type\Accessory\AccessoryNumericStringType;
use PHPStan\Type\Accessory\NonEmptyArrayType;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BenevolentUnionType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
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

class SiteDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	/** @var array<string, string> */
	private $siteApiGetAttributeMapping;

	/**
	 * @param array<string, string> $siteApiGetAttributeMapping
	 */
	public function __construct(array $siteApiGetAttributeMapping)
	{
		$this->siteApiGetAttributeMapping = $siteApiGetAttributeMapping;
	}

	public function getClass(): string
	{
		return \TYPO3\CMS\Core\Site\Entity\Site::class;
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$argument = $methodCall->getArgs()[0] ?? null;

		if ($argument === null || !($argument->value instanceof \PhpParser\Node\Scalar\String_)) {
			return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
		}

		if (isset($this->siteApiGetAttributeMapping[$argument->value->value])) {
			switch (strtolower($this->siteApiGetAttributeMapping[$argument->value->value])) {
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
		}

		return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
	}

	public function isMethodSupported(
		MethodReflection $methodReflection
	): bool
	{
		return $methodReflection->getName() === 'getAttribute';
	}

}
