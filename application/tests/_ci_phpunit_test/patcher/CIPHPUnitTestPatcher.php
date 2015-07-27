<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestPatcher
{
	private static $cache_dir;
	private static $load_patchers = false;
	private static $patcher_classnames = [];

	public static function setCacheDir($dir)
	{
		self::$cache_dir = $dir;
		self::createDir($dir);
		self::loadPatchers();
	}

	public static function getCacheDir()
	{
		return self::$cache_dir;
	}

	protected static function createDir($dir)
	{
		if (! is_dir($dir))
		{
			if (! @mkdir($dir, 0777, true))
			{
				throw new RuntimeException('Failed to create folder: ' . $dir);
			}
		}
	}

	/**
	 * @param string $path original source file path
	 * @return boolean
	 */
	protected static function hasValidCache($path)
	{
		$cache_file = self::getCacheFilePath($path);

		if (
			is_readable($cache_file) && filemtime($cache_file) > filemtime($path)
		)
		{
			return true;
		}

		return false;
	}

	protected static function getCacheFilePath($path)
	{
		$root = realpath(APPPATH . '../');
		$len = strlen($root);
		$relative_path = substr($path, $len);
		return self::$cache_dir . $relative_path;
	}

	public static function patch($path)
	{
		if (self::$cache_dir === null)
		{
			throw new LogicException('You have to set $cache_dir');
		}

		// Check cache file
		if (self::hasValidCache($path))
		{
			return fopen(self::getCacheFilePath($path), 'r');
		}

		$source = file_get_contents($path);

		list($new_source, $patched) = self::execPatchers($source);

		// Write to cache file
		self::writeCacheFile($path, $new_source);

		$resource = fopen('php://memory', 'rb+');
		fwrite($resource, $new_source);
		rewind($resource);
		return $resource;
	}

	/**
	 * @param string $path   original source file path
	 * @param string $source source code
	 */
	protected static function writeCacheFile($path, $source)
	{
		$cache_file = self::getCacheFilePath($path);
		$dir = dirname($cache_file);
		self::createDir($dir);
		file_put_contents($cache_file, $source);
	}

	protected static function loadPatchers()
	{
		if (self::$load_patchers)
		{
			return;
		}

		foreach (glob(__DIR__.'/patchers/*Patcher.php') as $patcher)
		{
			require $patcher;

			$classname = basename($patcher, '.php');
			self::$patcher_classnames[] = $classname;
		}

		self::$load_patchers = true;
	}

	protected static function execPatchers($source)
	{
		$patched = false;
		foreach (self::$patcher_classnames as $classname)
		{
			list($source, $patched_this) = $classname::patch($source);
			$patched = $patched || $patched_this;
		}

		return [
			$source,
			$patched,
		];
	}
}
