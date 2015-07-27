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

		self::loadCIPHPUnitTestClasses();

		// Replace a few Common functions
		require __DIR__ . '/replacing/core/Common.php';
		require BASEPATH . 'core/Common.php';

		// Load new functions of CIPHPUnitTest
		require __DIR__ . '/functions.php';

		self::replaceLoader();
		self::replaceHelpers();

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
			require_once BASEPATH . 'core/CodeIgniter.php';
		} catch (CIPHPUnitTestShow404Exception $e) {
			// Catch 404 exception
			new CI_Controller();
		}

		require APPPATH . '/tests/TestCase.php';

		// Restore $_SERVER
		$_SERVER = $_server_backup;

		if (isset(TestCase::$enable_patcher) && TestCase::$enable_patcher)
		{
			self::enablePatcher();
		}
	}

	protected static function replaceLoader()
	{
		require __DIR__ . '/replacing/core/Loader.php';
		$my_loader_file = APPPATH . 'core/' . config_item('subclass_prefix') . 'Loader.php';
		if (file_exists($my_loader_file))
		{
			self::$loader_class = config_item('subclass_prefix') . 'Loader';
			require $my_loader_file;
		}
		self::loadLoader();
	}

	protected static function replaceHelpers()
	{
		$my_helper_file = APPPATH . 'helpers/' . config_item('subclass_prefix') . 'url_helper.php';
		if (file_exists($my_helper_file))
		{
			require $my_helper_file;
		}
		require __DIR__ . '/replacing/helpers/url_helper.php';
	}

	protected static function loadCIPHPUnitTestClasses()
	{
		require __DIR__ . '/CIPHPUnitTestCase.php';
		require __DIR__ . '/CIPHPUnitTestRequest.php';
		require __DIR__ . '/CIPHPUnitTestDouble.php';
		require __DIR__ . '/exceptions/CIPHPUnitTestRedirectException.php';
		require __DIR__ . '/exceptions/CIPHPUnitTestShow404Exception.php';
		require __DIR__ . '/exceptions/CIPHPUnitTestShowErrorException.php';
		require __DIR__ . '/exceptions/CIPHPUnitTestExitException.php';
	}

	protected static function enablePatcher()
	{
		require __DIR__ . '/patcher/CIPHPUnitTestIncludeStream.php';
		require __DIR__ . '/patcher/CIPHPUnitTestPatchPathChecker.php';
		require __DIR__ . '/patcher/CIPHPUnitTestPatcher.php';

		// Register include stream wrapper for monkey patching
		CIPHPUnitTestIncludeStream::wrap();

		CIPHPUnitTestPatchPathChecker::setWhitelistDir(
			[
				APPPATH,
			]
		);
		CIPHPUnitTestPatchPathChecker::setBlacklistDir(
			[
				realpath(APPPATH . '../vendor/'),
				APPPATH . 'tests/',
			]
		);

		self::setPatcherCacheDir();
	}

	public static function setPatcherCacheDir()
	{
		CIPHPUnitTestPatcher::setCacheDir(
			APPPATH . 'tests/_ci_phpunit_test/tmp/cache'
		);
	}

	public static function loadLoader()
	{
		$loader = new self::$loader_class;
		load_class_instance('Loader', $loader);
	}
}
