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
	private static $whitelist_dir = [];
	private static $blacklist_dir = [];

	protected static function realPath(array $dirs)
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

	public static function setWhitelistDirs(array $dir)
	{
		self::$whitelist_dir = self::realPath($dir);
	}

	public static function setBlacklistDirs(array $dir)
	{
		self::$blacklist_dir = self::realPath($dir);
	}

	public static function check($path)
	{
		// Whitelist first
		$is_white = false;
		foreach (self::$whitelist_dir as $white_dir) {
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
		foreach (self::$blacklist_dir as $black_dir) {
			$len = strlen($black_dir);
			if (substr($path, 0, $len) === $black_dir)
			{
				return false;
			}
		}

		return true;
	}
}
