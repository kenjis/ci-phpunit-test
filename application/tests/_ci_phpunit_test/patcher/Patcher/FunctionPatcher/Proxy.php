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

use Kenjis\MonkeyPatch\Patcher\FunctionPatcher;

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

		return call_user_func_array($function, $arguments);
	}

	public static function preg_replace(
		$pattern, $replacement, $subject, $limit = -1, &$count
	)
	{
		if (isset(self::$mocks['preg_replace']))
		{
			if (is_callable(self::$mocks['preg_replace']))
			{
				$callable = self::$mocks['preg_replace'];
				return call_user_func_array(
					$callable,
					[$pattern, $replacement, $subject, $limit, &$count]
				);
			}

			return self::$mocks['preg_replace'];
		}

		return preg_replace($pattern, $replacement, $subject, $limit, $count);
	}
}
