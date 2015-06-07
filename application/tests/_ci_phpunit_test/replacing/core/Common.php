<?php

// Load this file before loading "system/core/Common.php"

/**
 * Class registry
 * 
 * @staticvar array $_classes
 * @param string $class
 * @param string $directory
 * @param array $param
 * @param bool $reset
 * @param object $obj
 * @return object
 */
function &load_class(
	$class,
	$directory = 'libraries',
	$param = NULL,
	$reset = FALSE,
	$obj = NULL
)
{
	static $_classes = array();

	if ($reset)
	{
		// If Utf8 is instantiated twice,
		// error "Constant UTF8_ENABLED already defined" occurs
		$UTF8 = $_classes['Utf8'];
		$_classes = array(
			'Utf8' => $UTF8
		);
		$obj = new stdClass();
		return $obj;
	}

	// Register object directly
	if ($obj)
	{
		is_loaded($class);

		$_classes[$class] = $obj;
		return $_classes[$class];
	}

	// Does the class exist? If so, we're done...
	if (isset($_classes[$class]))
	{
		return $_classes[$class];
	}

	$name = FALSE;

	// Look for the class first in the local application/libraries folder
	// then in the native system/libraries folder
	foreach (array(APPPATH, BASEPATH) as $path)
	{
		if (file_exists($path.$directory.'/'.$class.'.php'))
		{
			$name = 'CI_'.$class;

			if (class_exists($name, FALSE) === FALSE)
			{
				require_once($path.$directory.'/'.$class.'.php');
			}

			break;
		}
	}

	// Is the request a class extension? If so we load it too
	if (file_exists(APPPATH.$directory.'/'.config_item('subclass_prefix').$class.'.php'))
	{
		$name = config_item('subclass_prefix').$class;

		if (class_exists($name, FALSE) === FALSE)
		{
			require_once(APPPATH.$directory.'/'.$name.'.php');
		}
	}

	// Did we find the class?
	if ($name === FALSE)
	{
		// Note: We use exit() rather then show_error() in order to avoid a
		// self-referencing loop with the Exceptions class
		set_status_header(503);
		echo 'Unable to locate the specified class: '.$class.'.php';
		exit(5); // EXIT_UNK_CLASS
	}

	// Keep track of what we just loaded
	is_loaded($class);

	$_classes[$class] = isset($param)
		? new $name($param)
		: new $name();
	return $_classes[$class];
}

/**
 * Keeps track of which libraries have been loaded.
 * 
 * @staticvar array $_is_loaded
 * @param string $class
 * @param bool $reset
 * @return array
 */
function &is_loaded($class = '', $reset = FALSE)
{
	static $_is_loaded = array();

	if ($reset)
	{
		$_is_loaded = array();
		return $_is_loaded;
	}

	if ($class !== '')
	{
		$_is_loaded[strtolower($class)] = $class;
	}

	return $_is_loaded;
}

function is_cli($return = null)
{
	static $_return = TRUE;

	if ($return !== null)
	{
		$_return = $return;
	}

	return $_return;
}

function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
{
	throw new PHPUnit_Framework_Exception($message, $status_code);
}

function show_404($page = '', $log_error = TRUE)
{
	throw new PHPUnit_Framework_Exception($page, 404);
}
