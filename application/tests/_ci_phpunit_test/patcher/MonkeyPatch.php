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

use Kenjis\MonkeyPatch\Patcher\FunctionPatcher\Proxy;
use Kenjis\MonkeyPatch\Patcher\MethodPatcher\PatchManager;

class MonkeyPatch
{
	/**
	 * Patch on function
	 * 
	 * @param string $function     function name
	 * @param mixed  $return_value return value
	 */
	public static function patchFunction($function, $return_value)
	{
		Proxy::patch__($function, $return_value);
	}

	/**
	 * Reset all patched fuctions
	 */
	public static function resetFunctions()
	{
		Proxy::reset__();
	}

	/**
	 * Patch on class method
	 * 
	 * @param string $class
	 * @param array $params [method_name => return_value]
	 */
	public static function patchMethod($class, $params)
	{
		PatchManager::set($class, $params);
	}

	/**
	 * Reset all patched class method
	 */
	public static function resetMethods()
	{
		PatchManager::clear();
	}

	public static function verifyInvokedMultipleTimes(
		$class_method, $times, $params = null
	)
	{
		PatchManager::setExpectedInvocations(
			$class_method, $times, $params
		);
	}

	public static function verifyInvocations()
	{
		PatchManager::verifyInvocations();
	}
}
