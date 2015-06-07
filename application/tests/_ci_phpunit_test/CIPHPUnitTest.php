<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTest
{
	public static function init()
	{
		// Fix CLI args
		$_server_backup = $_SERVER;
		$_SERVER['argv'] = [
			'index.php',
		];
		$_SERVER['argc'] = 1;

		// Replace a few Common functions
		require __DIR__ . '/replacing/core/Common.php';
		require BASEPATH . 'core/Common.php';

		require __DIR__ . '/functions.php';

		// Replace Loader
		require BASEPATH . 'core/Loader.php';
		require __DIR__ . '/replacing/core/Loader.php';
		$loader = new CITEST_Loader();
		load_class_instance('Loader', $loader);

		require __DIR__ . '/autoloader.php';

		// Change current directroy
		chdir(FCPATH);

		/*
		 * --------------------------------------------------------------------
		 * LOAD THE BOOTSTRAP FILE
		 * --------------------------------------------------------------------
		 *
		 * And away we go...
		 */
		ob_start();
		require_once BASEPATH . 'core/CodeIgniter.php';
		ob_end_clean();

		require __DIR__ . '/CIPHPUnitTestCase.php';
		require __DIR__ . '/../TestCase.php';

		// Restore $_SERVER
		$_SERVER = $_server_backup;
	}
}
