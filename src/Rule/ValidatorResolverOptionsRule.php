<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Rule;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\InitializerExprTypeResolver;
use PHPStan\Reflection\Php\PhpPropertyReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use SaschaEgerer\PhpstanTypo3\Rule\ValueObject\ValidatorOptionsConfiguration;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;
use TYPO3\CMS\Extbase\Validation\ValidatorClassNameResolver;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;

/**
 * @implements Rule<MethodCall>
 */
final class ValidatorResolverOptionsRule implements Rule
{

	/** @var InitializerExprTypeResolver */
	private $initializerExprTypeResolver;

	public function __construct(InitializerExprTypeResolver $initializerExprTypeResolver)
	{
		$this->initializerExprTypeResolver = $initializerExprTypeResolver;
	}

	public function getNodeType(): string
	{
		return MethodCall::class;
	}

	/**
	 * @param MethodCall $node
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if ($this->shouldSkip($node, $scope)) {
			return [];
		}

		$validatorTypeArgument = $node->getArgs()[0] ?? null;
		$validatorOptionsArgument = $node->getArgs()[1] ?? null;

		if ($validatorTypeArgument === null) {
			return [];
		}

		$validatorType = $scope->getType($validatorTypeArgument->value);

		try {
			$validatorClassName = $this->extractValidatorClassName($validatorType);
		} catch (\TYPO3\CMS\Extbase\Validation\Exception\NoSuchValidatorException $e) {
			return [
				RuleErrorBuilder::message($e->getMessage())->build(),
			];
		}

		if ($validatorClassName === null) {
			return [];
		}

		$validatorObjectType = new ObjectType($validatorClassName);
		$validatorClassReflection = $validatorObjectType->getClassReflection();

		if ( ! $validatorClassReflection instanceof ClassReflection) {
			return [];
		}

		if (!$validatorClassReflection->isSubclassOf(AbstractValidator::class)) {
			return [];
		}

		try {
			$supportedOptions = $validatorClassReflection->getProperty('supportedOptions', $scope);
		} catch (\PHPStan\Reflection\MissingPropertyFromReflectionException $e) {
			return [];
		}

		$validatorOptionsConfiguration = $this->extractValidatorOptionsConfiguration($supportedOptions, $scope);
		$providedOptionsArray = $this->extractProvidedOptions($validatorOptionsArgument, $scope);

		$unsupportedOptions = array_diff($providedOptionsArray, $validatorOptionsConfiguration->getSupportedOptions());
		$neededRequiredOptions = array_diff($validatorOptionsConfiguration->getRequiredOptions(), $providedOptionsArray);

		$errors = [];

		if ($neededRequiredOptions !== []) {
			foreach ($neededRequiredOptions as $neededRequiredOption) {
				$errorMessage = sprintf('Required validation option not set: %s', $neededRequiredOption);
				$errors[] = RuleErrorBuilder::message($errorMessage)->build();
			}
		}

		if ($unsupportedOptions !== []) {
			$errorMessage = 'Unsupported validation option(s) found: ' . implode(', ', $unsupportedOptions);
			$errors[] = RuleErrorBuilder::message($errorMessage)->build();
		}

		return $errors;
	}

	private function shouldSkip(MethodCall $methodCall, Scope $scope): bool
	{
		$objectType = $scope->getType($methodCall->var);
		$validatorResolverType = new ObjectType(ValidatorResolver::class);

		if ($validatorResolverType->isSuperTypeOf($objectType)->no()) {
			return true;
		}

		if ( ! $methodCall->name instanceof Node\Identifier) {
			return true;
		}

		return $methodCall->name->toString() !== 'createValidator';
	}

	private function extractValidatorClassName(Type $type): ?string
	{
		if ($type instanceof ConstantStringType) {
			return ValidatorClassNameResolver::resolve($type->getValue());
		}

		return null;
	}

	/**
	 * @return string[]
	 */
	private function extractProvidedOptions(?Arg $validatorOptionsArgument, Scope $scope): array
	{
		if ( ! $validatorOptionsArgument instanceof Arg) {
			return [];
		}

		$providedOptionsArray = [];

		$validatorOptionsArgumentType = $scope->getType($validatorOptionsArgument->value);

		if ( ! $validatorOptionsArgumentType instanceof ConstantArrayType) {
			return [];
		}

		$keysArray = $validatorOptionsArgumentType->getKeysArray();

		foreach ($keysArray->getValueTypes() as $valueType) {
			if ( ! ($valueType instanceof ConstantStringType)) {
				continue;
			}

			$providedOptionsArray[] = $valueType->getValue();
		}

		return $providedOptionsArray;
	}

	private function extractValidatorOptionsConfiguration(PropertyReflection $supportedOptions, Scope $scope): ValidatorOptionsConfiguration
	{
		$collectedSupportedOptions = [];
		$collectedRequiredOptions = [];

		if ( ! $supportedOptions instanceof PhpPropertyReflection) {
			return ValidatorOptionsConfiguration::empty();
		}

		$defaultValues = $supportedOptions->getNativeReflection()->getDefaultValueExpr();

		if ( ! $defaultValues instanceof Array_) {
			return ValidatorOptionsConfiguration::empty();
		}

		foreach ($defaultValues->items as $defaultValue) {

			if ( ! $defaultValue instanceof ArrayItem) {
				continue;
			}

			if ($defaultValue->key === null) {
				continue;
			}

			$supportedOptionKey = $this->resolveOptionKeyValue($defaultValue, $supportedOptions, $scope);

			if ($supportedOptionKey === null) {
				continue;
			}

			$collectedSupportedOptions[] = $supportedOptionKey;

			$optionDefinition = $defaultValue->value;
			if ( ! $optionDefinition instanceof Array_) {
				continue;
			}

			if ( ! isset($optionDefinition->items[3])) {
				continue;
			}

			$requiredValueType = $scope->getType($optionDefinition->items[3]->value);

			if (!$requiredValueType instanceof ConstantBooleanType) {
				continue;
			}

			if (!$requiredValueType->getValue()) {
				continue;
			}

			$collectedRequiredOptions[] = $supportedOptionKey;
		}

		return new ValidatorOptionsConfiguration($collectedSupportedOptions, $collectedRequiredOptions);
	}

	private function resolveOptionKeyValue(ArrayItem $defaultValue, PhpPropertyReflection $supportedOptions, Scope $scope): ?string
	{
		if ($defaultValue->key === null) {
			return null;
		}

		if ($defaultValue->key instanceof ClassConstFetch && $defaultValue->key->name instanceof Node\Identifier) {
			$keyType = $this->initializerExprTypeResolver->getClassConstFetchType(
				$defaultValue->key->class,
				$defaultValue->key->name->toString(),
				$supportedOptions->getDeclaringClass()->getName(),
				static function (\PhpParser\Node\Expr $expr) use ($scope): Type {
					return $scope->getType($expr);
				}
			);

			if ($keyType instanceof ConstantStringType) {
				return $keyType->getValue();
			}

			return null;
		}

		$keyType = $scope->getType($defaultValue->key);

		if ($keyType instanceof ConstantStringType) {
			return $keyType->getValue();
		}

		return null;
	}

}
