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
	/**
	 * Request to Controller
	 * 
	 * @param string $method HTTP method
	 * @param array $argv    controller, method [, arg1, ...]
	 * @param array $params  POST parameters/Query string
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
}
