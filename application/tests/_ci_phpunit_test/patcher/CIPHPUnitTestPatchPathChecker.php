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

	public static function setWhitelistDir(array $dir)
	{
		self::$whitelist_dir = $dir;
	}

	public static function setBlacklistDir(array $dir)
	{
		self::$blacklist_dir = $dir;
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
