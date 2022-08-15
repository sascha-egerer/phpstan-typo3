# TYPO3 extension for PHPStan

TYPO3 CMS class reflection extension for PHPStan &amp; framework-specific rules

[![Build Status](https://travis-ci.org/sascha-egerer/phpstan-typo3.svg?branch=master)](https://travis-ci.org/sascha-egerer/phpstan-typo3)

## Configuration

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
