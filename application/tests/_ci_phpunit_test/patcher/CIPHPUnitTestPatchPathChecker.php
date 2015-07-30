<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestPatchPathChecker
{
	private static $include_paths = [];
	private static $exclude_paths = [];

	protected static function normalizePaths(array $dirs)
	{
		$new_dirs = [];
		foreach ($dirs as $dir)
		{
			$real_dir = realpath($dir);
			if ($real_dir === FALSE)
			{
				throw new RuntimeException($dir . ' does not exist?');
			}
			$new_dirs[] = $real_dir . '/';
		}
		return $new_dirs;
	}

	public static function setIncludePaths(array $dir)
	{
		self::$include_paths = self::normalizePaths($dir);
	}

	public static function setExcludePaths(array $dir)
	{
		self::$exclude_paths = self::normalizePaths($dir);
	}

	public static function check($path)
	{
		// Whitelist first
		$is_white = false;
		foreach (self::$include_paths as $white_dir) {
			$len = strlen($white_dir);
			if (substr($path, 0, $len) === $white_dir)
			{
				$is_white = true;
			}
		}
		if ($is_white === false)
		{
			return false;
		}

		// Then blacklist
		foreach (self::$exclude_paths as $black_dir) {
			$len = strlen($black_dir);
			if (substr($path, 0, $len) === $black_dir)
			{
				return false;
			}
		}

		return true;
	}
}
