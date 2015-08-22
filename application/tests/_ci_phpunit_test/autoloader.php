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
	$dirs = [APPPATH.'libraries', APPPATH.'controllers'];

	foreach ($dirs as $dir)
	{
		foreach (glob($dir.'/'.$class.'.php') as $class_file)
		{
			require_once $class_file;
			return;
		}
		foreach (glob($dir.'/*/'.$class.'.php') as $class_file)
		{
			require_once $class_file;
			return;
		}
	}
});

// Register CodeIgniter's tests/mocks/autoloader.php
define('SYSTEM_PATH', BASEPATH);
require APPPATH .'tests/mocks/autoloader.php';
spl_autoload_register('autoload');
