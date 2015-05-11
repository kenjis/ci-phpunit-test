<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

// Autoloader for testing
spl_autoload_register(function ($class)
{
	// Load mock libraries for testing
	if (substr($class, 0, 15) === 'Mock_Libraries_')
	{
		$tmp = explode('_', $class);
		$name = strtolower(array_pop($tmp));
		require_once __DIR__ . '/mocks/libraries/' . $name . '.php';
		return;
	}

	// Load controllers
	foreach (glob(APPPATH.'controllers/'.$class.'.php') as $controller)
	{
		require_once $controller;
	}
});
