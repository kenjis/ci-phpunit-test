<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Patcher\FunctionPatcher;

class_alias('Kenjis\MonkeyPatch\Patcher\FunctionPatcher\Proxy', '__FuncProxy__');

use LogicException;
use ReflectionFunction;

use Kenjis\MonkeyPatch\Patcher\FunctionPatcher;
use Kenjis\MonkeyPatch\MonkeyPatchManager;

class Proxy
{
	private static $mocks = [];

	public static function patch__($function, $return_value)
	{
		if (FunctionPatcher::isBlacklisted($function))
		{
			throw new LogicException('Can\'t patch on ' . $function);
		}

		self::$mocks[$function] = $return_value;
	}

	public static function reset__()
	{
		self::$mocks = [];
	}

	public static function __callStatic($function, array $arguments)
	{
		if (isset(self::$mocks[$function]))
		{
			if (is_callable(self::$mocks[$function]))
			{
				$callable = self::$mocks[$function];
				return call_user_func_array($callable, $arguments);
			}

			return self::$mocks[$function];
		}

		self::checkPassedByReference($function);

		return call_user_func_array($function, $arguments);
	}

	protected static function checkPassedByReference($function)
	{
		$ref_func = new ReflectionFunction($function);

		foreach ($ref_func->getParameters() as $param)
		{
			if ($param->isPassedByReference())
			{
				// Add tmp blacklist
				$tmp_blacklist_file = MonkeyPatchManager::getTmpBlacklistFile();
				var_dump($tmp_blacklist_file);
				file_put_contents(
					$tmp_blacklist_file, $function . "\n", FILE_APPEND
				);

				// Remove cache file
				$backtrace = debug_backtrace();
				$orig_file = $backtrace[1]['file'];
				$cache = MonkeyPatchManager::getSrcCacheFilePath($orig_file);
				@unlink($cache);

				$msg = '';
				if (self::isInternalFunction($function))
				{
					$msg = "\n" . 'Please send Pull Request to add function "' . $function . '" to default config.';
				}

				throw new LogicException(
					'Can\'t patch on function "' . $function . '".'
					. ' It has reference param.' . "\n"
					. 'Added it tmp blacklist file "'
					. $tmp_blacklist_file . '". ' . $msg . "\n"
					. 'And removed cache file "' . $cache . '".' . "\n"
				);
			}
		}
	}

	/**
	 * @param string $name function name
	 * @return bool
	 */
	protected static function isInternalFunction($name)
	{
		try {
			$ref_func = new ReflectionFunction($name);
			return $ref_func->isInternal();
		} catch (ReflectionException $e) {
			// ReflectionException: Function xxx() does not exist
			return false;
		}
	}

	/**
	 * If we define method like this, we can pass reference to callable,
	 * but we always need to pass 5th param, otherwise, error ocurrs.
	 * So this does not work well.
	 */
//	public static function preg_replace(
//		$pattern, $replacement, $subject, $limit = -1, &$count
//	)
//	{
//		if (isset(self::$mocks['preg_replace']))
//		{
//			if (is_callable(self::$mocks['preg_replace']))
//			{
//				$callable = self::$mocks['preg_replace'];
//				return call_user_func_array(
//					$callable,
//					[$pattern, $replacement, $subject, $limit, &$count]
//				);
//			}
//
//			return self::$mocks['preg_replace'];
//		}
//
//		return preg_replace($pattern, $replacement, $subject, $limit, $count);
//	}
}
