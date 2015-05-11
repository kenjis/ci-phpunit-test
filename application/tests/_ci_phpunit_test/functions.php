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
	load_class($classname, '', '', '', $instance);
}

/**
 * Get new CodeIgniter instance
 * 
 * @return CI_Controller
 */
function get_new_instance()
{
	// Reset loaded classes
	load_class('', '', '', TRUE);
	is_loaded('', TRUE);

	// Load core classes
	load_class('Benchmark', 'core');
	$EXT =& load_class('Hooks', 'core');
	$EXT->call_hook('pre_system');
	load_class('Config', 'core');
//	load_class('Utf8', 'core');
	load_class('URI', 'core');
	load_class('Router', 'core', isset($routing) ? $routing : NULL);
	load_class('Output', 'core');
	load_class('Security', 'core');
	load_class('Input', 'core');
	load_class('Lang', 'core');
	$EXT->call_hook('pre_controller');
	
	$loader = new CITEST_Loader();
	load_class_instance('Loader', $loader);
	$EXT->call_hook('post_controller_constructor');

	return new CI_Controller();
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
