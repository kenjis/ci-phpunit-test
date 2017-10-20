<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

// If you use Composer
if (class_exists('PhpParser\Autoloader')) {
	if (method_exists('PhpParser\Node\Name','set')) {
		// PHP-Parser 2.x
		require __DIR__ . '/2.x/MonkeyPatchManager.php';
	} else {
		// PHP-Parser 3.x
		require __DIR__ . '/3.x/MonkeyPatchManager.php';
	}
}
// If you don't use Composer
else {
	if (version_compare(PHP_VERSION, '5.5.0', '>=')) {
		// Use PHP-Parser 3.x
		require __DIR__ . '/third_party/PHP-Parser-3.0.3/lib/bootstrap.php';
		require __DIR__ . '/3.x/MonkeyPatchManager.php';
	} else {
		// Use PHP-Parser 2.x
		require __DIR__ . '/third_party/PHP-Parser-2.1.1/lib/bootstrap.php';
		require __DIR__ . '/2.x/MonkeyPatchManager.php';
	}
}

require __DIR__ . '/IncludeStream.php';
require __DIR__ . '/PathChecker.php';
require __DIR__ . '/MonkeyPatch.php';
require __DIR__ . '/Cache.php';
require __DIR__ . '/InvocationVerifier.php';

require __DIR__ . '/functions/exit__.php';

const __GO_TO_ORIG__ = '__GO_TO_ORIG__';

class_alias('Kenjis\MonkeyPatch\MonkeyPatchManager', 'MonkeyPatchManager');

// And you have to configure for your application
//MonkeyPatchManager::init([
//	// PHP Parser: PREFER_PHP7, PREFER_PHP5, ONLY_PHP7, ONLY_PHP5
//	'php_parser' => 'PREFER_PHP5',
//	// Project root directory
//	'root_dir' => APPPATH . '../',
//	// Cache directory
//	'cache_dir' => TESTPATH . '_ci_phpunit_test/tmp/cache',
//	// Directories to patch on source files
//	'include_paths' => [
//		APPPATH,
//		BASEPATH,
//	],
//	// Excluding directories to patch
//	'exclude_paths' => [
//		TESTPATH,
//	],
//	// All patchers you use
//	'patcher_list' => [
//		'ExitPatcher',
//		'FunctionPatcher',
//		'MethodPatcher',
//		'ConstantPatcher',
//	],
//	// Additional functions to patch
//	'functions_to_patch' => [
//		//'random_string',
//	],
//]);
