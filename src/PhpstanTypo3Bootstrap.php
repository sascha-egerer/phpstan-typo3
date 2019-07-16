<?php declare(strict_types = 1);

// phpcs:disable PSR1.Files.SideEffects
call_user_func(function (): void {
	// disable TYPO3_DLOG
	define('TYPO3_DLOG', false);

	$testbase = new \TYPO3\TestingFramework\Core\Testbase();
	$testbase->enableDisplayErrors();
	$testbase->defineBaseConstants();
	$testbase->defineSitePath();
	$testbase->defineTypo3ModeBe();
	$testbase->setTypo3TestingContext();
	$testbase->definePackagesPath();
	$testbase->createDirectory(PATH_site . 'typo3conf/ext');
	$testbase->createDirectory(PATH_site . 'typo3temp/assets');
	$testbase->createDirectory(PATH_site . 'typo3temp/var/tests');
	$testbase->createDirectory(PATH_site . 'typo3temp/var/transient');
	$testbase->createDirectory(PATH_site . 'uploads');

	// Retrieve an instance of class loader and inject to core bootstrap
	$classLoaderFilepath = TYPO3_PATH_PACKAGES . 'autoload.php';
	if (!file_exists($classLoaderFilepath)) {
		die('ClassLoader can\'t be loaded. Please check your path or set an environment variable \'TYPO3_PATH_ROOT\' to your root path.');
	}
	$classLoader = require $classLoaderFilepath;
	\TYPO3\CMS\Core\Core\Bootstrap::getInstance()
		->initializeClassLoader($classLoader)
		->setRequestType(TYPO3_REQUESTTYPE_BE | TYPO3_REQUESTTYPE_CLI)
		->baseSetup();

	// Initialize default TYPO3_CONF_VARS
	$configurationManager = new \TYPO3\CMS\Core\Configuration\ConfigurationManager();
	$GLOBALS['TYPO3_CONF_VARS'] = $configurationManager->getDefaultConfiguration();
	// Avoid failing tests that rely on HTTP_HOST retrieval
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['trustedHostsPattern'] = '.*';

	\TYPO3\CMS\Core\Core\Bootstrap::getInstance()
		->disableCoreCache()
		->initializeCachingFramework()
		// Set all packages to active
		->initializePackageManagement(\TYPO3\CMS\Core\Package\UnitTestPackageManager::class);

	if (\TYPO3\CMS\Core\Core\Bootstrap::usesComposerClassLoading()) {
		return;
	}

	// Dump autoload info if in non composer mode
	\TYPO3\CMS\Core\Core\ClassLoadingInformation::dumpClassLoadingInformation();
	\TYPO3\CMS\Core\Core\ClassLoadingInformation::registerClassLoadingInformation();
});
