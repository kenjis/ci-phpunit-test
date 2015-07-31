<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

require __DIR__ . '/CIPHPUnitTestIncludeStream.php';
require __DIR__ . '/CIPHPUnitTestPatchPathChecker.php';
require __DIR__ . '/CIPHPUnitTestPatcher.php';
require __DIR__ . '/MonkeyPatch.php';

// Register include stream wrapper for monkey patching
CIPHPUnitTestPatcher::wrap();

// And you have to configure for your application
//CIPHPUnitTestPatcher::init([
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
