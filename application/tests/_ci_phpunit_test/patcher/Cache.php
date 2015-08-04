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
	private static $tmp_function_whitelist_file;

	public static function setCacheDir($dir)
	{
		self::createDir($dir);
		self::$cache_dir = realpath($dir);
		self::$src_cache_dir = self::$cache_dir . '/src';
		self::$tmp_function_whitelist_file = 
			self::$cache_dir . '/conf/func_whiltelist.php';
		self::$tmp_function_blacklist_file = 
			self::$cache_dir . '/conf/func_blacklist.php';
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
		if (is_readable(self::$tmp_function_blacklist_file))
		{
			return;
		}

		$dir = dirname(self::$tmp_function_blacklist_file);
		self::createDir($dir);
		touch(self::$tmp_function_blacklist_file);
	}

	public static function appendTmpFunctionBlacklist($function)
	{
		file_put_contents(
			self::getTmpFunctionBlacklistFile(), $function . "\n", FILE_APPEND
		);
	}

	public static function writeTmpFunctionWhitelist(array $functions)
	{
		$contents = implode("\n", $functions);
		file_put_contents(
			self::$tmp_function_whitelist_file, $contents
		);
	}

	public static function getTmpFunctionWhitelist()
	{
		if (is_readable(self::$tmp_function_whitelist_file))
		{
			return file(
				self::$tmp_function_whitelist_file,
				FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
			);
		}
		return [];
	}

	public static function removeSrcCacheFile($orig_file)
	{
		$cache = self::getSrcCacheFilePath($orig_file);
		@unlink($cache);
		MonkeyPatchManager::log('remove_src_cache: ' . $cache);
	}

	public static function clearSrcCache()
	{
		self::recursiveUnlink(self::$src_cache_dir);
		MonkeyPatchManager::log('clear_src_cache:');
	}

	public static function clearCache()
	{
		self::recursiveUnlink(self::$cache_dir);
		MonkeyPatchManager::log('clear_cache:');
	}

	/**
	* Recursive Unlink
	*
	* @param string $dir
	*/
	protected static function recursiveUnlink($dir)
	{
		if (! is_dir($dir))
		{
			return;
		}

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
