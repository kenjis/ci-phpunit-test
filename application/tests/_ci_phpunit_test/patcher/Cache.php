<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch;

use RuntimeException;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Cache
{
	private static $cache_dir;
	private static $src_cache_dir;
	private static $tmp_function_blacklist_file;

	public static function setCacheDir($dir)
	{
		self::createDir($dir);
		self::$cache_dir = realpath($dir);
		self::$src_cache_dir = self::$cache_dir . '/src';
	}

	public static function getCacheDir()
	{
		return self::$cache_dir;
	}

	public static function getSrcCacheFilePath($path)
	{
		$root = realpath(APPPATH . '../');	// @TODO depends on APPPATH
		$len = strlen($root);
		$relative_path = substr($path, $len);

		if ($relative_path === false)
		{
			return false;
		}

		return self::$src_cache_dir . '/' . $relative_path;
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
	public static function hasValidSrcCache($path)
	{
		$cache_file = self::getSrcCacheFilePath($path);

		if (
			is_readable($cache_file) && filemtime($cache_file) > filemtime($path)
		)
		{
			return true;
		}

		return false;
	}

	/**
	 * Write to src cache file
	 * 
	 * @param string $path   original source file path
	 * @param string $source source code
	 */
	public static function writeSrcCacheFile($path, $source)
	{
		$cache_file = self::getSrcCacheFilePath($path);
		self::writeCacheFile($cache_file, $source);
	}

	/**
	 * Write to cache file
	 * 
	 * @param string $path   file path
	 * @param string $contents file contents
	 */
	public static function writeCacheFile($path, $contents)
	{
		$dir = dirname($path);
		self::createDir($dir);
		file_put_contents($path, $contents);
	}

	public static function getTmpFunctionBlacklistFile()
	{
		return self::$tmp_function_blacklist_file;
	}

	public static function createTmpFunctionBlacklistFile()
	{
		$tmp_blacklist = self::getCacheDir() . '/conf/func_blacklist.php';
		self::$tmp_function_blacklist_file = $tmp_blacklist;

		if (is_readable($tmp_blacklist))
		{
			return;
		}

		$dir = dirname($tmp_blacklist);
		self::createDir($dir);
		touch($tmp_blacklist);
	}

	public static function removeSrcCacheFile($orig_file)
	{
		$cache = self::getSrcCacheFilePath($orig_file);
		@unlink($cache);
	}

	public static function clearSrcCache()
	{
		self::recursiveUnlink(self::$src_cache_dir);
	}

	public static function clearCache()
	{
		self::recursiveUnlink(self::$cache_dir);
	}

	/**
	* Recursive Unlink
	*
	* @param string $dir
	*/
	protected static function recursiveUnlink($dir)
	{
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(
				$dir, RecursiveDirectoryIterator::SKIP_DOTS
			),
			RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ($iterator as $file) {
			if ($file->isDir()) {
				rmdir($file);
			} else {
				unlink($file);
			}
		}

		rmdir($dir);
	}
}
