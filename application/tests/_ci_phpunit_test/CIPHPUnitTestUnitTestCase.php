<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestUnitTestCase extends CIPHPUnitTestCase
{
	/**
	 * Create a controller instance
	 *
	 * @param string $classname
	 * @return CI_Controller
	 */
	public function newController($classname)
	{
		reset_instance();
		$controller = new $classname;
		$this->CI =& get_instance();
		return $controller;
	}

	/**
	 * Create a model instance
	 *
	 * @param string $classname
	 * @return CI_Model
	 */
	public function newModel($classname)
	{
		$this->resetInstance();
		$this->CI->load->model($classname);

		// Is the model in a sub-folder?
		if (($last_slash = strrpos($classname, '/')) !== FALSE)
		{
			$classname = substr($classname, ++$last_slash);
		}

		return $this->CI->$classname;
	}

	/**
	 * Create a library instance
	 *
	 * @param string $classname
	 * @param array  $args
	 * @return object
	 */
	public function newLibrary($classname, $args = null)
	{
		$this->resetInstance();
		$this->CI->load->library($classname, $args);

		// Is the library in a sub-folder?
		if (($last_slash = strrpos($classname, '/')) !== FALSE)
		{
			$classname = substr($classname, ++$last_slash);
		}
		$classname = strtolower($classname);

		return $this->CI->$classname;
	}
}
