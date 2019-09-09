# TYPO3 extension for PHPStan

TYPO3 CMS class reflection extension for PHPStan &amp; framework-specific rules

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

## Additional Configuration

If you do use constants of TYPO3 core you may have to
bootstrap TYPO3 first. This can be done by using the
unit testing bootstrap of the testing-framework

```
parameters:
    autoload_files:
        - %rootDir%/../../typo3/testing-framework/Resources/Core/Build/UnitTestsBootstrap.php
```
