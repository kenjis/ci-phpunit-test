<?php

class CIPHPUnitTestPatcher_test extends PHPUnit_Framework_TestCase
{
	public static function tearDownAfterClass()
	{
		CIPHPUnitTestPatcher::setCacheDir(APPPATH . 'tests/tmp/cache');
		self::recursiveUnlink(APPPATH . 'tests/tmp/cache/application/tests');
	}

	public static function recursiveUnlink($dir)
	{
		if (! is_dir($dir)) {
			return;
		}

		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ($iterator as $file) {
			if ($file->isDir()) {
				rmdir($file);
			} else {
				unlink($file);
			}
		}

		rmdir($dir);
	}

	public static function getPrivateMethodInvoker($class, $method)
	{
		$ref_method = new ReflectionMethod($class, $method);
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
			$ref_class = new ReflectionObject($class);
		} else {
			$ref_class = new ReflectionClass($class);
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

	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionMessage Failed to create folder:
	 */
	public function test_setCacheDir_error()
	{
		CIPHPUnitTestPatcher::setCacheDir(null);
	}

	/**
	 * @expectedException LogicException
	 * @expectedExceptionMessage You have to set $cache_dir
	 */
	public function test_patch_error()
	{
		CIPHPUnitTestPatcher::patch('dummy');
	}

	public function test_patch_miss_cache()
	{
		CIPHPUnitTestPatcher::setCacheDir(APPPATH . 'tests/tmp/cache');

		CIPHPUnitTestPatcher::patch(__FILE__);

		$orig = file_get_contents(__FILE__);
		$method = self::getPrivateMethodInvoker('CIPHPUnitTestPatcher', 'getCacheFilePath');
		$cache = file_get_contents($method(__FILE__));
		$this->assertEquals($orig, $cache);
	}
}
