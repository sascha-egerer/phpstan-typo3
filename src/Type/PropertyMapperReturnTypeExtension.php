<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Type;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use TYPO3\CMS\Extbase\Property\PropertyMapper;

final class PropertyMapperReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	/** @var ReflectionProvider */
	private $reflectionProvider;

	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
	}

	public function getClass(): string
	{
		return PropertyMapper::class;
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'convert';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): ?Type
	{
		$targetTypeArgument = $methodCall->getArgs()[1] ?? null;

		if ($targetTypeArgument === null) {
			return null;
		}

		$argumentValue = $targetTypeArgument->value;

		if ($argumentValue instanceof ClassConstFetch) {
			/** @var Name $class */
			$class = $argumentValue->class;
			return TypeCombinator::addNull(new ObjectType((string) $class));
		}

		if ($argumentValue instanceof String_) {
			return $this->createTypeFromString($argumentValue);
		}

		return null;
	}

	private function createTypeFromString(String_ $node): ?Type
	{
		if ($node->value === 'array') {
			return TypeCombinator::addNull(new ArrayType(new MixedType(), new MixedType()));
		}

		if ($node->value === 'string') {
			return TypeCombinator::addNull(new StringType());
		}

		if ($node->value === 'boolean') {
			return TypeCombinator::addNull(new BooleanType());
		}

		if ($node->value === 'integer') {
			return TypeCombinator::addNull(new IntegerType());
		}

		return $this->createTypeFromClassNameString($node);
	}

	private function createTypeFromClassNameString(String_ $node): ?Type
	{
		$classLikeName = $node->value;

		// remove leading slash
		$classLikeName = ltrim($classLikeName, '\\');
		if ($classLikeName === '') {
			return null;
		}

		if (!$this->reflectionProvider->hasClass($classLikeName)) {
			return null;
		}

		$classReflection = $this->reflectionProvider->getClass($classLikeName);
		if ($classReflection->getName() !== $classLikeName) {
			return null;
		}

		// possibly string
		if (ctype_lower($classLikeName[0])) {
			return null;
		}

		return TypeCombinator::addNull(new ObjectType($classLikeName));
	}

}
