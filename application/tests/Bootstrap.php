<?php
/*
 *---------------------------------------------------------------
 * SYSTEM FOLDER NAME
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" folder.
 * Include the path if the folder is not in the same directory
 * as this file.
 */
	$system_path = '../../vendor/codeigniter/framework/system';

/*
 *---------------------------------------------------------------
 * APPLICATION FOLDER NAME
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * folder than the default one you can set its name here. The folder
 * can also be renamed or relocated anywhere on your server. If
 * you do, use a full server path. For more info please see the user guide:
 * http://codeigniter.com/user_guide/general/managing_apps.html
 *
 * NO TRAILING SLASH!
 */
	$application_folder = '../application';

/*
 *---------------------------------------------------------------
 * VIEW FOLDER NAME
 *---------------------------------------------------------------
 *
 * If you want to move the view folder out of the application
 * folder set the path to the folder here. The folder can be renamed
 * and relocated anywhere on your server. If blank, it will default
 * to the standard location inside your application folder. If you
 * do move this, use the full server path to this folder.
 *
 * NO TRAILING SLASH!
 */
	$view_folder = '';

// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 */
	define('ENVIRONMENT', 'testing');

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 */
	error_reporting(-1);
	ini_set('display_errors', 1);


/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

	// Set the current directory correctly for CLI requests
	if (defined('STDIN'))
	{
		chdir(dirname(__FILE__));
	}

	if (($_temp = realpath($system_path)) !== FALSE)
	{
		$system_path = $_temp.'/';
	}
	else
	{
		// Ensure there's a trailing slash
		$system_path = rtrim($system_path, '/').'/';
	}

	// Is the system path correct?
	if ( ! is_dir($system_path))
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this: '.pathinfo(__FILE__, PATHINFO_BASENAME);
		exit(3); // EXIT_CONFIG
	}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
	// The name of THIS file
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	// Path to the system folder
	define('BASEPATH', str_replace('\\', '/', $system_path));

	// Path to the front controller (this file)
	define('FCPATH', realpath(dirname(__FILE__).'/../../public').'/');

	// Name of the "system folder"
	define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

	// The path to the "application" folder
	if (is_dir($application_folder))
	{
		if (($_temp = realpath($application_folder)) !== FALSE)
		{
			$application_folder = $_temp;
		}

		define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
	}
	else
	{
		if ( ! is_dir(BASEPATH.$application_folder.DIRECTORY_SEPARATOR))
		{
			header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
			echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
			exit(3); // EXIT_CONFIG
		}

		define('APPPATH', BASEPATH.$application_folder.DIRECTORY_SEPARATOR);
	}

	// The path to the "views" folder
	if ( ! is_dir($view_folder))
	{
		if ( ! empty($view_folder) && is_dir(APPPATH.$view_folder.DIRECTORY_SEPARATOR))
		{
			$view_folder = APPPATH.$view_folder;
		}
		elseif ( ! is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR))
		{
			header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
			echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
			exit(3); // EXIT_CONFIG
		}
		else
		{
			$view_folder = APPPATH.'views';
		}
	}

	if (($_temp = realpath($view_folder)) !== FALSE)
	{
		$view_folder = $_temp.DIRECTORY_SEPARATOR;
	}
	else
	{
		$view_folder = rtrim($view_folder, '/\\').DIRECTORY_SEPARATOR;
	}

	define('VIEWPATH', $view_folder);

// Fix CLI args
$_SERVER['argv'] = [
    FCPATH . 'index.php',
];
$_SERVER['argc'] = 1;

require __DIR__ . '/replace/core/Common.php';
require BASEPATH . 'core/Common.php';

// Replace Loader
require BASEPATH . 'core/Loader.php';
require __DIR__ . '/replace/core/Loader.php';
$loader = new CITEST_Loader();
load_class_instance('Loader', $loader);

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
require_once BASEPATH.'core/CodeIgniter.php';
ob_end_clean();

// Autoloader for testing
spl_autoload_register(function ($class)
{
	// Load mock libraries for testing
	if (substr($class, 0, 15) === 'Mock_Libraries_')
	{
		$tmp = explode('_', $class);
		$name = strtolower(array_pop($tmp));
		require_once __DIR__ . '/mocks/libraries/' . $name . '.php';
		return;
	}

	// Load controllers
	foreach (glob(APPPATH.'controllers/'.$class.'.php') as $controller)
	{
		require_once $controller;
	}
});

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

require __DIR__ . '/TestCase.php';
