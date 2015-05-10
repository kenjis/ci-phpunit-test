<?php
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
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Loader Class
 *
 * Loads framework components.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Loader
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/loader.html
 */
class CITEST_Loader extends \CI_Loader {
	/**
	 * Model Loader
	 *
	 * Loads and instantiates models.
	 *
	 * @param	string	$model		Model name
	 * @param	string	$name		An optional object name to assign to
	 * @param	bool	$db_conn	An optional database connection configuration to initialize
	 * @return	object
	 */
	public function model($model, $name = '', $db_conn = FALSE)
	{
		if (empty($model))
		{
			return $this;
		}
		elseif (is_array($model))
		{
			foreach ($model as $key => $value)
			{
				is_int($key) ? $this->model($value, '', $db_conn) : $this->model($key, $value, $db_conn);
			}

			return $this;
		}

		$path = '';

		// Is the model in a sub-folder? If so, parse out the filename and path.
		if (($last_slash = strrpos($model, '/')) !== FALSE)
		{
			// The path is in front of the last slash
			$path = substr($model, 0, ++$last_slash);

			// And the model name behind it
			$model = substr($model, $last_slash);
		}

		if (empty($name))
		{
			$name = $model;
		}

		if (in_array($name, $this->_ci_models, TRUE))
		{
			return $this;
		}

		$CI =& get_instance();
		if (isset($CI->$name))
		{
			show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
		}

		if ($db_conn !== FALSE && ! class_exists('CI_DB', FALSE))
		{
			if ($db_conn === TRUE)
			{
				$db_conn = '';
			}

			$this->database($db_conn, FALSE, TRUE);
		}

//		if ( ! class_exists('CI_Model', FALSE))
//		{
			load_class('Model', 'core');
//		}

		$model = ucfirst(strtolower($model));

		foreach ($this->_ci_model_paths as $mod_path)
		{
			if ( ! file_exists($mod_path.'models/'.$path.$model.'.php'))
			{
				continue;
			}

			require_once($mod_path.'models/'.$path.$model.'.php');

			$this->_ci_models[] = $name;
			$CI->$name = new $model();
			return $this;
		}

		// couldn't find the model
		show_error('Unable to locate the model you have specified: '.$model);
	}

	// --------------------------------------------------------------------

	/**
	 * Internal CI Library Loader
	 *
	 * @used-by	CI_Loader::library()
	 * @uses	CI_Loader::_ci_init_library()
	 *
	 * @param	string	$class		Class name to load
	 * @param	mixed	$params		Optional parameters to pass to the class constructor
	 * @param	string	$object_name	Optional object name to assign to
	 * @return	void
	 */
	protected function _ci_load_library($class, $params = NULL, $object_name = NULL)
	{
		// Get the class name, and while we're at it trim any slashes.
		// The directory path can be included as part of the class name,
		// but we don't want a leading slash
		$class = str_replace('.php', '', trim($class, '/'));

		// Was the path included with the class name?
		// We look for a slash to determine this
		if (($last_slash = strrpos($class, '/')) !== FALSE)
		{
			// Extract the path
			$subdir = substr($class, 0, ++$last_slash);

			// Get the filename from the path
			$class = substr($class, $last_slash);
		}
		else
		{
			$subdir = '';
		}

		$class = ucfirst($class);

		// Is this a stock library? There are a few special conditions if so ...
		if (file_exists(BASEPATH.'libraries/'.$subdir.$class.'.php'))
		{
			return $this->_ci_load_stock_library($class, $subdir, $params, $object_name);
		}

		// Let's search for the requested library file and load it.
		foreach ($this->_ci_library_paths as $path)
		{
			// BASEPATH has already been checked for
			if ($path === BASEPATH)
			{
				continue;
			}

			$filepath = $path.'libraries/'.$subdir.$class.'.php';

			// Safety: Was the class already loaded by a previous call?
//			if (class_exists($class, FALSE))
//			{
				// Before we deem this to be a duplicate request, let's see
				// if a custom object name is being supplied. If so, we'll
				// return a new instance of the object
				if ($object_name !== NULL)
				{
					$CI =& get_instance();
					if ( ! isset($CI->$object_name))
					{
						return $this->_ci_init_library($class, '', $params, $object_name);
					}
				}

//				log_message('debug', $class.' class already loaded. Second attempt ignored.');
//				return;
//			}
			// Does the file exist? No? Bummer...
			if ( ! file_exists($filepath))
			{
				continue;
			}

			include_once($filepath);
			return $this->_ci_init_library($class, '', $params, $object_name);
		}

		// One last attempt. Maybe the library is in a subdirectory, but it wasn't specified?
		if ($subdir === '')
		{
			return $this->_ci_load_library($class.'/'.$class, $params, $object_name);
		}

		// If we got this far we were unable to find the requested class.
		log_message('error', 'Unable to load the requested class: '.$class);
		show_error('Unable to load the requested class: '.$class);
	}

	// --------------------------------------------------------------------

	/**
	 * Internal CI Stock Library Loader
	 *
	 * @used-by	CI_Loader::_ci_load_library()
	 * @uses	CI_Loader::_ci_init_library()
	 *
	 * @param	string	$library	Library name to load
	 * @param	string	$file_path	Path to the library filename, relative to libraries/
	 * @param	mixed	$params		Optional parameters to pass to the class constructor
	 * @param	string	$object_name	Optional object name to assign to
	 * @return	void
	 */
	protected function _ci_load_stock_library($library_name, $file_path, $params, $object_name)
	{
		$prefix = 'CI_';

//		if (class_exists($prefix.$library_name, FALSE))
		{
			if (class_exists(config_item('subclass_prefix').$library_name, FALSE))
			{
				$prefix = config_item('subclass_prefix');
			}

			// Before we deem this to be a duplicate request, let's see
			// if a custom object name is being supplied. If so, we'll
			// return a new instance of the object
			if ($object_name !== NULL)
			{
				$CI =& get_instance();
				if ( ! isset($CI->$object_name))
				{
					return $this->_ci_init_library($library_name, $prefix, $params, $object_name);
				}
			}

//			log_message('debug', $library_name.' class already loaded. Second attempt ignored.');
//			return;
		}

		$paths = $this->_ci_library_paths;
		array_pop($paths); // BASEPATH
		array_pop($paths); // APPPATH (needs to be the first path checked)
		array_unshift($paths, APPPATH);

		foreach ($paths as $path)
		{
			if (file_exists($path = $path.'libraries/'.$file_path.$library_name.'.php'))
			{
				// Override
				include_once($path);
//				if (class_exists($prefix.$library_name, FALSE))
				{
					return $this->_ci_init_library($library_name, $prefix, $params, $object_name);
				}
//				else
//				{
//					log_message('debug', $path.' exists, but does not declare '.$prefix.$library_name);
//				}
			}
		}

		include_once(BASEPATH.'libraries/'.$file_path.$library_name.'.php');

		// Check for extensions
		$subclass = config_item('subclass_prefix').$library_name;
		foreach ($paths as $path)
		{
			if (file_exists($path = $path.'libraries/'.$file_path.$subclass.'.php'))
			{
				include_once($path);
				if (class_exists($subclass, FALSE))
				{
					$prefix = config_item('subclass_prefix');
					break;
				}
				else
				{
					log_message('debug', APPPATH.'libraries/'.$file_path.$subclass.'.php exists, but does not declare '.$subclass);
				}
			}
		}

		return $this->_ci_init_library($library_name, $prefix, $params, $object_name);
	}
}
