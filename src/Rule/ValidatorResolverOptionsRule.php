<?php
declare(strict_types=1);


namespace SaschaEgerer\PhpstanTypo3\Rule;


use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MissingPropertyFromReflectionException;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\Php\PhpPropertyReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
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

		$validatorType = $validatorTypeArgument->value;

		$validatorClassName = $this->extractValidatorClassName($validatorType);

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
		} catch (MissingPropertyFromReflectionException $e) {
			return [];
		}

		$collectedSupportedOptions = [];
		$collectedRequiredOptions = [];

		if ($supportedOptions instanceof PhpPropertyReflection) {
			$defaultValues = $supportedOptions->getNativeReflection()->getDefaultValueExpr();

			if($defaultValues instanceof Array_) {
				foreach ($defaultValues->items as $defaultValue) {

					if(!$defaultValue instanceof ArrayItem) {
						continue;
					}

					if ($defaultValue->key === null) {
						continue;
					}

					$optionName = $defaultValue->key->value;

					$collectedSupportedOptions[] = $optionName;
					$optionDefinition = $defaultValue->value;
					if ( ! $optionDefinition instanceof Array_) {
						continue;
					}
					if ( ! isset($optionDefinition->items[3])) {
						continue;
					}
					$requiredValue = $optionDefinition->items[3]->value;

					if ($requiredValue instanceof ConstFetch && $requiredValue->name->toString() === "true") {
						$collectedRequiredOptions[] = $optionName;
					}
				}
			}
		}

		$providedOptionsArray = [];
		if ($validatorOptionsArgument !== null && $validatorOptionsArgument->value instanceof Array_) {
			foreach ($validatorOptionsArgument->value->items as $providedOption) {
				if(!$providedOption instanceof ArrayItem) {
					continue;
				}

				if($providedOption->key === null) {
					continue;
				}

				$providedOptionsArray[] = $providedOption->key->value;
			}
		}

		$unsupportedOptions = array_diff($providedOptionsArray, $collectedSupportedOptions);
		$neededRequiredOptions = array_diff($collectedRequiredOptions, $providedOptionsArray);

		$errors = [];

		if ($neededRequiredOptions !== []) {
			foreach ($neededRequiredOptions as $neededRequiredOption) {
				$errorMessage = sprintf('Required validation option not set: %s', $neededRequiredOption);
				$errors[] = RuleErrorBuilder::message($errorMessage)->build();
			}
		}

		if ($unsupportedOptions !== []) {
			$errorMessage = 'Unsupported validation option(s) found: '.implode(', ', $unsupportedOptions);
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

	private function extractValidatorClassName(Expr $validatorType): ?string
	{
		if ($validatorType instanceof ClassConstFetch && $validatorType->class instanceof Node\Name) {
			return $validatorType->class->toString();
		}

		if ($validatorType instanceof String_) {
			return ValidatorClassNameResolver::resolve($validatorType->value);
		}

		return null;
	}
}
