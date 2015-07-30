<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestFunctionPatcherProxy
{
	private static $mocks = [];

	public static function mock($function, $returnValue)
	{
		if (CIPHPUnitTestFunctionPatcher::isBlacklisted($function))
		{
			throw new LogicException('Can\'t patch on ' . $function);
		}

		self::$mocks[$function] = $returnValue;
	}

	public static function reset()
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
}
