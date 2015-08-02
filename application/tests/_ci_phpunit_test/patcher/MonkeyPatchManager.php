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

use LogicException;
use RuntimeException;

use Kenjis\MonkeyPatch\Patcher\FunctionPatcher;

class MonkeyPatchManager
{
	private static $cache_dir;
	private static $load_patchers = false;
	private static $exit_exception_classname = 
		'Kenjis\MonkeyPatch\Exception\ExitException';
	private static $tmp_blacklist_file;
	/**
	 * @var array list of patcher classname
	 */
	private static $patcher_list = [
		'ExitPatcher',
		'FunctionPatcher',
		'MethodPatcher',
	];

	public static function setExitExceptionClassname($name)
	{
		self::$exit_exception_classname = $name;
	}

	public static function getExitExceptionName()
	{
		return self::$exit_exception_classname;
	}

	public static function init(array $config)
	{
		if (! isset($config['cache_dir']))
		{
			throw new LogicException('You have to set "cache_dir"');
		}
		self::setCacheDir($config['cache_dir']);

		if (! isset($config['include_paths']))
		{
			throw new LogicException('You have to set "include_paths"');
		}
		self::setIncludePaths($config['include_paths']);

		if (isset($config['exclude_paths']))
		{
			self::setExcludePaths($config['exclude_paths']);
		}

		if (isset($config['patcher_list']))
		{
			self::setPatcherList($config['patcher_list']);
		}

		if (isset($config['exit_exception_classname']))
		{
			self::setExitExceptionClassname($config['exit_exception_classname']);
		}

		self::loadPatchers();
		self::createTmpBlacklistFile();
		self::addTmpBlacklist();
	}

	public static function getTmpBlacklistFile()
	{
		return self::$tmp_blacklist_file;
	}

	protected static function addTmpBlacklist()
	{
		$list = file(self::$tmp_blacklist_file);
		foreach ($list as $function)
		{
			FunctionPatcher::addBlacklist(trim($function));
		}
	}

	protected static function createTmpBlacklistFile()
	{
		$tmp_blacklist = self::getCacheDir() . '/conf/func_blacklist.php';
		self::$tmp_blacklist_file = $tmp_blacklist;

		if (is_readable($tmp_blacklist))
		{
			return;
		}

		$dir = dirname($tmp_blacklist);
		self::createDir($dir);
		touch($tmp_blacklist);
	}

	public static function isEnabled($patcher)
	{
		return in_array($patcher, self::$patcher_list);
	}

	public static function setPatcherList(array $list)
	{
		if (self::$load_patchers)
		{
			throw new LogicException('Can\'t change patcher list after init');
		}

		self::$patcher_list = $list;
	}

	public static function setCacheDir($dir)
	{
		self::createDir($dir);
		self::$cache_dir = realpath($dir);
		
	}

	public static function getCacheDir()
	{
		return self::$cache_dir;
	}

	public static function setIncludePaths(array $dir_list)
	{
		PathChecker::setIncludePaths($dir_list);
	}

	public static function setExcludePaths(array $dir_list)
	{
		PathChecker::setExcludePaths($dir_list);
	}

	public static function wrap()
	{
		IncludeStream::wrap();
	}

	public static function unwrap()
	{
		IncludeStream::unwrap();
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
	protected static function hasValidSrcCache($path)
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

	public static function getSrcCacheFilePath($path)
	{
		$root = realpath(APPPATH . '../');
		$len = strlen($root);
		$relative_path = substr($path, $len);

		if ($relative_path === false)
		{
			return false;
		}

		return self::$cache_dir . '/src' . $relative_path;
	}

	/**
	 * @param string $path original source file path
	 * @return resource
	 * @throws LogicException
	 */
	public static function patch($path)
	{
		if (self::$cache_dir === null)
		{
			throw new LogicException('You have to set "cache_dir"');
		}

		if (! is_readable($path))
		{
			throw new LogicException('Can\'t read "' . $path . '"');
		}

		// Check cache file
		if (self::hasValidSrcCache($path))
		{
			return fopen(self::getSrcCacheFilePath($path), 'r');
		}

		$source = file_get_contents($path);

		list($new_source, $patched) = self::execPatchers($source);

		// Write to cache file
		self::writeSrcCacheFile($path, $new_source);

		$resource = fopen('php://memory', 'rb+');
		fwrite($resource, $new_source);
		rewind($resource);
		return $resource;
	}

	/**
	 * @param string $path   original source file path
	 * @param string $source source code
	 */
	protected static function writeSrcCacheFile($path, $source)
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

	protected static function loadPatchers()
	{
		if (self::$load_patchers)
		{
			return;
		}

		foreach (self::$patcher_list as $classname)
		{
			require __DIR__ . '/Patcher/' . $classname . '.php';
		}

		self::$load_patchers = true;
	}

	protected static function execPatchers($source)
	{
		$patched = false;
		foreach (self::$patcher_list as $classname)
		{
			$classname = 'Kenjis\MonkeyPatch\Patcher\\' . $classname;
			list($source, $patched_this) = $classname::patch($source);
			$patched = $patched || $patched_this;
		}

		return [
			$source,
			$patched,
		];
	}
}
