<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

require __DIR__ . '/IncludeStream.php';
require __DIR__ . '/PathChecker.php';
require __DIR__ . '/MonkeyPatchManager.php';
require __DIR__ . '/MonkeyPatch.php';

require __DIR__ . '/functions/exit__.php';

const __GO_ORIG_METHOD__ = '__GO_ORIG_METHOD__';

class_alias('Kenjis\MonkeyPatch\MonkeyPatch', 'MonkeyPatch');
class_alias('Kenjis\MonkeyPatch\MonkeyPatchManager', 'MonkeyPatchManager');

// Register include stream wrapper for monkey patching
MonkeyPatchManager::wrap();

// And you have to configure for your application
//MonkeyPatchManager::init([
//	'cache_dir' => APPPATH . 'tests/_ci_phpunit_test/tmp/cache',
//	// Directories to patch on source files
//	'include_paths' => [
//		APPPATH,
//		BASEPATH,
//	],
//	// Excluding directories to patch
//	'exclude_paths' => [
//		APPPATH . 'tests/',
//	],
//	// All patchers you use
//	'patcher_list' => [
//		'ExitPatcher',
//		'FunctionPatcher',
//		'MethodPatcher',
//	],
//]);
