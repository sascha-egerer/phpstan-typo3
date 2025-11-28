<?php declare(strict_types = 1);

/**
 * Attempt to find the vendor directory in a generic way by traversing upwards
 * from the current directory until a vendor/autoload.php file is found.
 * Allows overriding via COMPOSER_VENDOR_DIR environment variable.
 */
function findVendorDir(string $startDir, int $maxLevels = 12): ?string
{
	$envVendor = getenv('COMPOSER_VENDOR_DIR');
	if (is_string($envVendor) && $envVendor !== '' && is_dir($envVendor)) {
		$autoloadFile = rtrim($envVendor, '/\\') . '/autoload.php';
		if (file_exists($autoloadFile)) {
			return rtrim($envVendor, '/\\');
		}
	}

	$dir = $startDir;
	for ($i = 0; $i < $maxLevels; $i++) {
		$candidate = $dir . '/vendor';
		if (is_dir($candidate) && file_exists($candidate . '/autoload.php')) {
			return $candidate;
		}
		$parent = dirname($dir);
		if ($parent === $dir) {
			break; // Reached filesystem root
		}
		$dir = $parent;
	}
	return null;
}

$vendorDir = findVendorDir(dirname(__DIR__));

if ($vendorDir === null) {
	throw new \RuntimeException('Could not find vendor directory', 8904766827);
}

// Only require autoloader if not already loaded
$autoloaderFile = $vendorDir . '/autoload.php';
if (!class_exists(\Composer\Autoload\ClassLoader::class, false) && file_exists($autoloaderFile)) {
	require_once $autoloaderFile;
}
