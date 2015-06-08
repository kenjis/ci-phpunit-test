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
	// Load controllers
	foreach (glob(APPPATH.'controllers/'.$class.'.php') as $controller)
	{
		require_once $controller;
		return;
	}
	foreach (glob(APPPATH.'controllers/*/'.$class.'.php') as $controller)
	{
		require_once $controller;
		return;
	}
});

// Register CodeIgniter's tests/mocks/autoloader.php
define('SYSTEM_PATH', BASEPATH);
require APPPATH .'tests/mocks/autoloader.php';
spl_autoload_register('autoload');
