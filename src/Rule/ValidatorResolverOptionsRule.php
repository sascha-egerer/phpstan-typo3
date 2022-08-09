<?php declare(strict_types = 1);

namespace SaschaEgerer\PhpstanTypo3\Rule;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
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
use TYPO3\CMS\Extbase\Validation\ValidatorClassNameResolver;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;

/**
 * @implements Rule<MethodCall>
 */
final class ValidatorResolverOptionsRule implements Rule
{

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
		$classReflection = $validatorObjectType->getClassReflection();

		if ( ! $classReflection instanceof ClassReflection) {
			return [];
		}

		try {
			$supportedOptions = $classReflection->getProperty('supportedOptions', $scope);
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
			if (!($valueType instanceof ConstantStringType)) {
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

		if (!$supportedOptions instanceof PhpPropertyReflection) {
			return ValidatorOptionsConfiguration::empty();
		}

		$defaultValues = $supportedOptions->getNativeReflection()->getDefaultValueExpr();

		$supportedOptionsType = $scope->getType($defaultValues);

		if ( ! $supportedOptionsType instanceof ConstantArrayType) {
			return ValidatorOptionsConfiguration::empty();
		}

		$keysArray = $supportedOptionsType->getKeysArray();

		foreach ($keysArray->getValueTypes() as $valueType) {
			if (!($valueType instanceof ConstantStringType)) {
				continue;
			}

			$collectedSupportedOptions[] = $valueType->getValue();
		}

		$valuesArray = $supportedOptionsType->getValuesArray();
		foreach ($valuesArray->getValueTypes() as $index => $valueType) {
			if ( ! $valueType instanceof ConstantArrayType) {
				continue;
			}

			if ( ! isset($valueType->getValueTypes()[3])) {
				continue;
			}

			$requiredValueType = $valueType->getValueTypes()[3];

			if ( ! $requiredValueType instanceof ConstantBooleanType) {
				continue;
			}

			$keyType = $keysArray->getValueTypes()[$index];
			if ( ! $keyType instanceof ConstantStringType) {
				continue;
			}

			if (!$requiredValueType->getValue()) {
				continue;
			}

			$collectedRequiredOptions[] = $keyType->getValue();
		}

		return new ValidatorOptionsConfiguration($collectedSupportedOptions, $collectedRequiredOptions);
	}

}
