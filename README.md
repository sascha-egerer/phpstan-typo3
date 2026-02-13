# PHPStan TYPO3 extensions and rules

TYPO3 CMS class reflection extension for PHPStan &amp; framework-specific rules.

---

[![Build](https://github.com/sascha-egerer/phpstan-typo3/workflows/Tests/badge.svg)](https://github.com/sascha-egerer/phpstan-typo3/actions)

* [PHPStan](https://phpstan.org/)

This extension provides the following features (!!! not an exhaustive list !!!):

**Dynamic Return Type Extensions**
* Provides correct return type for `\TYPO3\CMS\Core\Context\Context->getAspect()`.
* Provides correct return type for `\TYPO3\CMS\Core\Context\DateTimeAspect->get()`.
* Provides correct return type for `\TYPO3\CMS\Core\Context\UserAspect->get()`.
* Provides correct return type for `\TYPO3\CMS\Extbase\Property\PropertyMapper->convert()`.
* Provides correct return type for `\TYPO3\CMS\Core\Utility\MathUtility` methods like `isIntegerInRange`.
* Provides correct return type for `\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv()`.
* Provides correct return type for `\TYPO3\CMS\Extbase\Persistence\QueryInterface->execute()`.
* Provides correct return type for `\TYPO3\CMS\Extbase\Persistence\ObjectStorage` methods.
* Provides correct return type for `\TYPO3\CMS\Core\Site\Entity\Site->getAttribute()`.
* Provides correct return type for `\Psr\Http\Message\ServerRequestInterface->getAttribute()`.
* Provides correct return type for `\TYPO3\CMS\Extbase\Validation\ValidatorResolver->createValidator()`.
* Uses under the hood [bnf/phpstan-psr-container](https://github.com/bnf/phpstan-psr-container)

All these dynamic return type extensions are necessary to teach PHPStan what type will be returned by the specific method call.

<details>
<summary>Show me a practical use case.</summary>
For example PHPStan cannot know innately what type will be returned if you call `\TYPO3\CMS\Core\Utility\MathUtility->forceIntegerInRange(1000, 1, 10)`.
It will be an int<10>. With the help of this library PHPStan also knows what´s going up.

Imagine the following situation in your code:

```php

use TYPO3\CMS\Core\Utility\MathUtility;

$integer = MathUtility::forceIntegerInRange(100, 1, 10);

if($integer > 10) {
    throw new \UnexpectedValueException('The integer is too big')
}
```

PHPStan will tell you that the if condition is superfluous, because the variable $integer will never be higher than 10. Right?
</details>

**Framework specific rules**
* Validates `\TYPO3\CMS\Core\Context\Context->getAspect()` aspect names.
* Validates `\Psr\Http\Message\ServerRequestInterface->getAttribute()` attribute names.
* Validates `\TYPO3\CMS\Core\Site\Entity\Site->getAttribute()` attribute names.
* Validates `\TYPO3\CMS\Extbase\Validation\ValidatorResolver->createValidator()` required options.
* Detects private service access via `\Psr\Container\ContainerInterface->get()`.
* Detects private service access via `\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance()`.

<details>
<summary>Show me a practical use case.</summary>

For example PHPStan cannot know innately that calling `ValidatorResolver->createValidator(RegularExpressionValidator::class)` is invalid, because we miss to pass the required option `regularExpression`.
With the help of this library PHPStan now complaints that we have missed to pass the required option.
So go ahead and find bugs in your code without running it.

</details>


## Installation & Configuration

To use this extension, require it in [Composer](https://getcomposer.org/):

```Shell
composer require --dev saschaegerer/phpstan-typo3
```

If you also install [phpstan/extension-installer](https://github.com/phpstan/extension-installer) then you're all set!

<details>
  <summary>Manual installation</summary>

If you don't want to use `phpstan/extension-installer`, put this into your phpstan.neon config:

```NEON
includes:
    - vendor/saschaegerer/phpstan-typo3/extension.neon
```

</details>

### Custom Context API Aspects

If you use custom aspects for the TYPO3 Context API you can add a mapping so PHPStan knows
what type of aspect class is returned by the context API

```NEON
parameters:
    typo3:
        contextApiGetAspectMapping:
            myCustomAspect: FlowdGmbh\MyProject\Context\MyCustomAspect
```

```PHP
// PHPStan will now know that $myCustomAspect is of type FlowdGmbh\MyProject\Context\MyCustomAspect
$myCustomAspect = GeneralUtility::makeInstance(Context::class)->getAspect('myCustomAspect');
```

### Custom Request Attribute

If you use custom PSR-7 request attribute you can add a mapping so PHPStan knows
what type of class is returned by Request::getAttribute()

```NEON
parameters:
    typo3:
        requestGetAttributeMapping:
            myAttribute: FlowdGmbh\MyProject\Http\MyAttribute
            myNullableAttribute: FlowdGmbh\MyProject\Http\MyAttribute|null
```

```PHP
// PHPStan will now know that $myAttribute is of type FlowdGmbh\MyProject\Http\MyAttribute
$myAttribute = $request->getAttribute('myAttribute');
```

### Custom Site Attribute

If you use custom attributes for the TYPO3 Site API you can add a mapping so PHPStan knows
what type is returned by the site API

```NEON
parameters:
    typo3:
        siteGetAttributeMapping:
            myArrayAttribute: array
            myIntAttribute: int
            myStringAttribute: string
```

```PHP
$site = $this->request->getAttribute('site');

// PHPStan will now know that $myArrayAttribute is of type array<mixed, mixed>
$myArrayAttribute = $site->getAttribute('myArrayAttribute');

// PHPStan will now know that $myIntAttribute is of type int
$myIntAttribute = $site->getAttribute('myIntAttribute');

// PHPStan will now know that $myStringAttribute is of type string
$myStringAttribute = $site->getAttribute('myStringAttribute');
```

### Check for private Services
You have to provide a path to App_KernelDevelopmentDebugContainer.xml or similar XML file describing your container.
This is generated by [ssch/typo3-debug-dump-pass](https://github.com/sabbelasichon/typo3-debug-dump-pass) in your /var/cache/{TYPO3_CONTEXT}/ folder.

```NEON
parameters:
    typo3:
        containerXmlPath: var/cache/development/App_KernelDevelopmentDebugContainer.xml
```

## Development

### Running Tests and Quality Checks

The following composer scripts are available for development:

| Command | Description |
|---------|-------------|
| `composer test` | Run all quality checks (lint, cs, phpstan, phpunit) |
| `composer test:php-lint` | Run PHP syntax linting using parallel-lint |
| `composer test:php-cs` | Run code style checks using PHP_CodeSniffer |
| `composer test:phpstan` | Run static analysis using PHPStan |
| `composer test:phpunit` | Run PHPUnit tests |
| `composer fix:php-cs` | Fix code style issues using PHP Code Beautifier |
