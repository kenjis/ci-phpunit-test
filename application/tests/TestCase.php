<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class TestCase extends PHPUnit_Framework_TestCase
{
	protected $_error_reporting = -1;

	public static function setUpBeforeClass()
	{
		// Fix ix CLI args, because you may set invalid URI characters
		// For example, you run tests on NetBeans
		$_SERVER['argv'] = [
			'index.php',
		];
		$_SERVER['argc'] = 1;
	}

	/**
	 * Request to Controller
	 * 
	 * @param string   $method   HTTP method
	 * @param array    $argv     controller, method [, arg1, ...]
	 * @param array    $params   POST parameters/Query string
	 * @param callable $callable
	 */
	public function request($method, $argv, $params = [], $callable = null)
	{
		$_SERVER['REQUEST_METHOD'] = $method;
		
		$_SERVER['argv'] = array_merge(['index.php'], $argv);
		
		if ($method === 'POST')
		{
			$_POST = $params;
		}
		elseif ($method === 'GET')
		{
			$_GET = $params;
		}
//		var_dump($_SERVER['REQUEST_METHOD'], $_SERVER['argv'], $_GET, $_POST); exit;
		
		$this->CI = get_new_instance();
		
		if (is_callable($callable))
		{
			$callable($this->CI);
		}
		
		array_shift($_SERVER['argv']);
		$controller = array_shift($_SERVER['argv']);
		$controller = ucfirst($controller);
		$method = array_shift($_SERVER['argv']);
		$this->obj = new $controller;
		ob_start();
		call_user_func_array([$this->obj, $method], $_SERVER['argv']);
		$output = ob_get_clean();
		
		return $output;
	}

	/**
	 * Get Mock Object
	 * 
	 * $email = $this->getMockBuilder('CI_Email')
	 *	->setMethods(['send'])
	 *	->getMock();
	 * $email->method('send')->willReturn(TRUE);
	 * 
	 *  will be
	 * 
	 * $email = $this->get_mock('CI_Email', ['send' => TRUE]);
	 * 
	 * @param string $classname
	 * @param array $params [method_name => return_value]
	 * @return object PHPUnit mock object
	 */
	public function get_mock($classname, $params)
	{
		$methods = array_keys($params);
		
		$mock = $this->getMockBuilder($classname)->setMethods($methods)
			->getMock();
		
		foreach ($params as $method => $return)
		{
			$mock->method($method)->willReturn($return);
		}
		
		return $mock;
	}

	public function warning_off()
	{
		$this->_error_reporting = error_reporting(E_ALL & ~E_WARNING);
	}

	public function warning_on()
	{
		error_reporting($this->_error_reporting);
	}
}
