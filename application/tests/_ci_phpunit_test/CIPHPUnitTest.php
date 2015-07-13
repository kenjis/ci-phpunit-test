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
	private static $loader_class = 'CI_Loader';

	public static function init()
	{
		// Fix CLI args
		$_server_backup = $_SERVER;
		$_SERVER['argv'] = [
			'index.php',
			'_dummy/_dummy'	// Force 404 route
		];
		$_SERVER['argc'] = 2;

		// Replace a few Common functions
		require __DIR__ . '/replacing/core/Common.php';
		require BASEPATH . 'core/Common.php';

		// Load new functions of CIPHPUnitTest
		require __DIR__ . '/functions.php';

		// Replace Loader
		require __DIR__ . '/replacing/core/Loader.php';
		$my_loader_file = APPPATH . 'core/' . config_item('subclass_prefix') . 'Loader.php';
		if (file_exists($my_loader_file))
		{
			self::$loader_class = config_item('subclass_prefix') . 'Loader';
			require $my_loader_file;
		}
		self::loadLoader();

		// Load autoloader for CIPHPUnitTest
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
		try {
			// Request to 404 route
			// This is needed for not to call Welcome::index()
			// If controller Welcome is called in bootstrap, we can't test
			// the same name sub controller Welcome even when we use
			// `@runInSeparateProcess` and `@preserveGlobalState disabled`
			ob_start();
			require_once BASEPATH . 'core/CodeIgniter.php';
			ob_end_clean();
		} catch (PHPUnit_Framework_Exception $e) {
			// Catch 404 exception
			new CI_Controller();
		}

		require __DIR__ . '/CIPHPUnitTestCase.php';
		require __DIR__ . '/CIPHPUnitTestRequest.php';
		require APPPATH . '/tests/TestCase.php';

		// Restore $_SERVER
		$_SERVER = $_server_backup;
	}

	public static function loadLoader()
	{
		$loader = new self::$loader_class;
		load_class_instance('Loader', $loader);
	}
}
