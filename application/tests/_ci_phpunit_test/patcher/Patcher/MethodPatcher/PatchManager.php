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

use Kenjis\MonkeyPatch\MonkeyPatchManager;

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
		if (MonkeyPatchManager::$debug)
		{
			$trace = debug_backtrace();
			$file = $trace[0]['file'];
			$line = $trace[0]['line'];
			$called_method = isset($trace[2]['class']) ? $trace[2]['class'].'::'.$trace[2]['function'] : $trace[2]['function'];
			
			$log_args = function () use ($params) {
				$output = '';
				foreach ($params as $arg) {
					$output .= var_export($arg, true) . ', ';
				}
				$output = rtrim($output, ', ');
				return $output;
			};
			MonkeyPatchManager::log(
				'invoke_method: ' . $class.'::'.$method . '(' . $log_args() . ') on line ' . $line . ' in ' . $file . ' by ' . $called_method . '()'
			);
//			var_dump($trace); exit;
		}

		self::$invocations[$class.'::'.$method][] = $params;

		$patch = isset(self::$patches[$class][$method]) ? self::$patches[$class][$method] : null;

		if ($patch === null)
		{
			return __GO_TO_ORIG__;
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
				list($expected_params, $expected_times) = $params_times;

				$invoked = isset(self::$invocations[$class_method]);
				if ($invoked === false)
				{
					$actual_times = 0;
				}
				elseif ($expected_params === null)
				{
					$actual_times = count(self::$invocations[$class_method]);
				}
				else
				{
					$count = 0;
					foreach (self::$invocations[$class_method] as $actual_params)
					{
						if ($actual_params == $expected_params)
						{
							$count++;
						}
					}
					$actual_times = $count;
				}

				if ($expected_times === 0)
				{
					PHPUnit_Framework_TestCase::assertEquals(
						$expected_times,
						$actual_times,
						$class_method . '() expected to be not invoked, but invoked ' . $actual_times . ' times.'
					);
				}
				elseif ($expected_times === '+')
				{
					PHPUnit_Framework_TestCase::assertGreaterThanOrEqual(
						1,
						$actual_times,
						$class_method . '() expected to be invoked at least one time, but invoked ' . $actual_times . ' times.'
					);
				}
				else
				{
					PHPUnit_Framework_TestCase::assertEquals(
						$expected_times,
						$actual_times,
						$class_method . '() expected to be invoked ' . $expected_times . ' times, but invoked ' . $actual_times . ' times.'
					);
				}
			}
		}
	}
}
