# TYPO3 extension for PHPStan

TYPO3 CMS class reflection extension for PHPStan &amp; framework-specific rules

[![Build Status](https://travis-ci.org/sascha-egerer/phpstan-typo3.svg?branch=master)](https://travis-ci.org/sascha-egerer/phpstan-typo3)

## Configuration

To use this extension, require it in [Composer](https://getcomposer.org/):

```
composer require --dev saschaegerer/phpstan-typo3
```

If you also install [phpstan/extension-installer](https://github.com/phpstan/extension-installer) then you're all set!

<details>
  <summary>Manual installation</summary>

If you don't want to use `phpstan/extension-installer`, put this into your phpstan.neon config:

```
includes:
    - vendor/saschaegerer/phpstan-typo3/extension.neon
```
</details>

### Custom Context API Aspects

If you use custom aspects for the TYPO3 Context API you can now add a mapping so PHPStan knows
what type of aspect class is returned by the context API

```
parameters:
    typo3:
        contextApiGetAspectMapping:
            myCustomAspect: FlowdGmbh\MyProject\Context\MyCustomAspect
```
