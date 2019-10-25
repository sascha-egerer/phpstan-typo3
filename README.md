# TYPO3 extension for PHPStan

TYPO3 CMS class reflection extension for PHPStan &amp; framework-specific rules

[![Build Status](https://travis-ci.org/sascha-egerer/phpstan-typo3.svg?branch=master)](https://travis-ci.org/sascha-egerer/phpstan-typo3)

## Configuration
Put this into your phpstan.neon config:

```
includes:
    - vendor/saschaegerer/phpstan-typo3/extension.neon
```

If you do use constants of TYPO3 core you may have to
bootstrap TYPO3 first. This can be done by using the
unit testing bootstrap of the testing-framework

```
parameters:
    autoload_files:
        - %rootDir%/../../typo3/testing-framework/Resources/Core/Build/UnitTestsBootstrap.php
```
