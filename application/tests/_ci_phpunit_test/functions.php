<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

/**
 * Inject instance to load_class() function
 * 
 * @param string $classname
 * @param object $instance
 */
function load_class_instance($classname, $instance)
{
	load_class($classname, '', NULL, FALSE, $instance);
}

/**
 * Reset CodeIgniter instance
 */
function reset_instance()
{
	// Reset loaded classes
	load_class('', '', NULL, TRUE);
	is_loaded('', TRUE);

	// Load core classes
	load_class('Benchmark', 'core');
	load_class('Hooks', 'core');
	load_class('Config', 'core');
//	load_class('Utf8', 'core');
	load_class('URI', 'core');
	load_class('Router', 'core');
	load_class('Output', 'core');
	load_class('Security', 'core');
	load_class('Input', 'core');
	load_class('Lang', 'core');
	
	CIPHPUnitTest::loadLoader();
}

/**
 * Get new CodeIgniter instance
 * @deprecated
 * 
 * @return CI_Controller
 */
function get_new_instance()
{
	reset_instance();
	
	$controller = new CI_Controller();
	return $controller;
}

/**
 * Set return value of is_cli() function
 * 
 * @param bool $return
 */
function set_is_cli($return)
{
	is_cli($return);
}
