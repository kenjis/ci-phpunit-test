<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestReflection
{
	public static function getPrivateMethodInvoker($class, $method)
	{
		$ref_method = new \ReflectionMethod($class, $method);
		$ref_method->setAccessible(true);
		$obj = (gettype($class) === 'object') ? $class : null;

		return function () use ($class, $ref_method, $obj) {
			$args = func_get_args();
			return $ref_method->invokeArgs($obj, $args);
		};
	}

	public static function setPrivateProperty($class, $property, $value)
	{
		if (is_object($class)) {
			$ref_class = new \ReflectionObject($class);
		} else {
			$ref_class = new \ReflectionClass($class);
		}

		$ref_property = $ref_class->getProperty($property);
		$ref_property->setAccessible(true);
		$ref_property->setValue($value);
	}

	public static function getPrivateProperty($class, $property)
	{
		if (is_object($class)) {
			$ref_class = new ReflectionObject($class);
		} else {
			$ref_class = new ReflectionClass($class);
		}

		$ref_property = $ref_class->getProperty($property);
		$ref_property->setAccessible(true);

		return $ref_property->getValue();
	}
}
