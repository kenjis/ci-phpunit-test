<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

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
		CIPHPUnitTestFunctionPatcherProxy::mock($function, $return_value);
	}

	/**
	 * Reset all patched fuctions
	 */
	public static function resetFunctions()
	{
		CIPHPUnitTestFunctionPatcherProxy::reset();
	}

	/**
	 * Patch on class method
	 * 
	 * @param string $class
	 * @param array $params [method_name => return_value]
	 */
	public static function patchMethod($class, $params)
	{
		CIPHPUnitTestMethodPatchManager::set($class, $params);
	}

	/**
	 * Reset all patched class method
	 */
	public static function resetMethods()
	{
		CIPHPUnitTestMethodPatchManager::clear();
	}

	public static function verifyInvokedMultipleTimes(
		$class_method, $times, $params = null
	)
	{
		CIPHPUnitTestMethodPatchManager::setExpectedInvocations(
			$class_method, $times, $params
		);
	}

	public static function verifyInvocations()
	{
		CIPHPUnitTestMethodPatchManager::verifyInvocations();
	}
}
