<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Patcher\MethodPatcher;

class_alias('Kenjis\MonkeyPatch\Patcher\MethodPatcher\PatchManager', '__PatchManager__');

use PHPUnit_Framework_TestCase;

class PatchManager
{
	private static $patches = [];
	private static $expected_invocations = [];
	private static $invocations = [];

	/**
	 * Set a method patch
	 * 
	 * @param string $class
	 * @param array $params [method_name => return_value]
	 */
	public static function set($class, $params)
	{
		self::$patches[$class] = $params;
	}

	/**
	 * Clear all patches and invocation data
	 */
	public static function clear()
	{
		self::$patches = [];
		self::$expected_invocations = [];
		self::$invocations = [];
	}

	public static function getReturn($class, $method, $params)
	{
		self::$invocations[$class.'::'.$method][] = $params;

		$patch = isset(self::$patches[$class][$method]) ? self::$patches[$class][$method] : null;

		if ($patch === null)
		{
			return __GO_ORIG_METHOD__;
		}

		if (is_callable($patch))
		{
			return call_user_func_array($patch, $params);
		} else {
			return $patch;
		}
	}

	public static function setExpectedInvocations($class_method, $times, $params)
	{
		self::$expected_invocations[$class_method][] = [$params, $times];
	}

	public static function verifyInvocations()
	{
		if (self::$expected_invocations === [])
		{
			return;
		}

		foreach (self::$expected_invocations as $class_method => $data)
		{
			foreach ($data as $params_times)
			{
				list($params, $times) = $params_times;
				
				if ($times === 0)
				{
					$invoked = isset(self::$invocations[$class_method]);
					
					PHPUnit_Framework_TestCase::assertFalse(
						$invoked,
						$class_method . '() expected to be not invoked, but invoked.'
					);
					
					continue;
				}

				if ($params === null)
				{
					$actual = count(self::$invocations[$class_method]);
					
					PHPUnit_Framework_TestCase::assertEquals(
						$times,
						$actual,
						$class_method . '() expected to be invoked ' . $times . ' times, but invoked ' . $actual . ' times.'
					);
				}
				else
				{
					$count = 0;
					foreach (self::$invocations[$class_method] as $actual_params)
					{
						if ($actual_params == $params)
						{
							$count++;
						}
					}
					
					$params_print = implode(', ', $params);
					PHPUnit_Framework_TestCase::assertEquals(
						$times,
						$count,
						$class_method . '(' . $params_print . ') expected to be invoked ' . $times . ' times, but invoked ' . $count . ' times.'
					);
				}
			}
		}
	}
}
