<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestCase extends PHPUnit_Framework_TestCase
{
	protected $_error_reporting = -1;

	public static function setUpBeforeClass()
	{
		// Fix CLI args, because you may set invalid URI characters
		// For example, you run tests on NetBeans
		$_SERVER['argv'] = [
			'index.php',
		];
		$_SERVER['argc'] = 1;
	}

	/**
	 * Request to Controller
	 *
	 * @param string       $http_method HTTP method
	 * @param array|string $argv        array of controller,method,arg|uri
	 * @param array        $params      POST parameters/Query string
	 * @param callable     $callable
	 */
	public function request($http_method, $argv, $params = [], $callable = null)
	{
		if (is_array($argv))
		{
			return $this->callControllerMethod(
				$http_method, $argv, $params, $callable
			);
		}
		else
		{
			return $this->requestUri($http_method, $argv, $params, $callable);
		}
	}

	/**
	 * Request to Controller using ajax request
	 *
	 * @param string       $http_method HTTP method
	 * @param array|string $argv        array of controller,method,arg|uri
	 * @param array        $params      POST parameters/Query string
	 * @param callable     $callable
	 */
	public function ajaxRequest($http_method, $argv, $params = [], $callable = null)
	{
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
		return $this->request($http_method, $argv, $params, $callable);
	}

	/**
	 * Call Controller Method
	 *
	 * @param string   $http_method HTTP method
	 * @param array    $argv        controller, method [, arg1, ...]
	 * @param array    $params      POST parameters/Query string
	 * @param callable $callable
	 */
	protected function callControllerMethod($http_method, $argv, $params = [], $callable = null)
	{
		$_SERVER['REQUEST_METHOD'] = $http_method;
		$_SERVER['argv'] = array_merge(['index.php'], $argv);

		if ($http_method === 'POST')
		{
			$_POST = $params;
		}
		elseif ($http_method === 'GET')
		{
			$_GET = $params;
		}

		$class  = ucfirst($argv[0]);
		$method = $argv[1];

		// Remove controller and method
		array_shift($argv);
		array_shift($argv);

//		$request = [
//			'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
//			'class' => $class,
//			'method' => $method,
//			'params' => $argv,
//			'$_GET' => $_GET,
//			'$_POST' => $_POST,
//		];
//		var_dump($request, $_SERVER['argv']);

		// Reset CodeIgniter instance state
		reset_instance();

		// 404 checking
		if (! class_exists($class) || ! method_exists($class, $method))
		{
			show_404($class.'::'.$method . '() is not found');
		}

		// Create controller
		$this->obj = new $class;
		$this->CI =& get_instance();
		if (is_callable($callable))
		{
			$callable($this->CI);
		}

		// Call controller method
		ob_start();
		call_user_func_array([$this->obj, $method], $argv);
		$output = ob_get_clean();

		if ($output == '')
		{
			$output = $this->CI->output->get_output();
		}

		return $output;
	}

	/**
	 * Request to URI
	 *
	 * @param string   $http_method HTTP method
	 * @param string   $uri         URI string
	 * @param array    $params      POST parameters/Query string
	 * @param callable $callable
	 */
	protected function requestUri($method, $uri, $params = [], $callable = null)
	{
		$_SERVER['REQUEST_METHOD'] = $method;
		$_SERVER['argv'] = ['index.php', $uri];

		if ($method === 'POST')
		{
			$_POST = $params;
		}
		elseif ($method === 'GET')
		{
			$_GET = $params;
		}

		// Force cli mode because if not, it changes URI (and RTR) behavior
		$cli = is_cli();
		set_is_cli(TRUE);

		// Reset CodeIgniter instance state
		reset_instance();

		// Get route
		$RTR =& load_class('Router', 'core');
		$URI =& load_class('URI', 'core');
		list($class, $method, $params) = $this->getRoute($RTR, $URI);

		// Restore cli mode
		set_is_cli($cli);

//		$request = [
//			'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
//			'class' => $class,
//			'method' => $method,
//			'params' => $params,
//			'$_GET' => $_GET,
//			'$_POST' => $_POST,
//		];
//		var_dump($request, $_SERVER['argv']);

		// Create controller
		$this->obj = new $class;
		$this->CI =& get_instance();
		if (is_callable($callable))
		{
			$callable($this->CI);
		}

		// Call controller method
		ob_start();
		call_user_func_array([$this->obj, $method], $params);
		$output = ob_get_clean();

		if ($output == '')
		{
			$output = $this->CI->output->get_output();
		}

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
	 * $email = $this->getDouble('CI_Email', ['send' => TRUE]);
	 *
	 * @param  string $classname
	 * @param  array  $params    [method_name => return_value]
	 * @return object PHPUnit mock object
	 */
	public function getDouble($classname, $params)
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

	protected function _verify($mock, $method, $params = null, $expects, $with)
	{
		$invocation = $mock->expects($expects)
			->method($method);

		$count = count($params);

		switch ($count) {
			case 0:
				break;
			case 1:
				$invocation->$with(
					$params[0]
				);
				break;
			case 2:
				$invocation->$with(
					$params[0], $params[1]
				);
				break;
			case 3:
				$invocation->$with(
					$params[0], $params[1], $params[2]
				);
				break;
			case 4:
				$invocation->$with(
					$params[0], $params[1], $params[2], $params[3]
				);
				break;
			case 5:
				$invocation->$with(
					$params[0], $params[1], $params[2], $params[3], $params[4], $params[5]
				);
				break;
			default:
				throw new RuntimeException(
					'Sorry, ' . $count . ' params not implemented yet'
				);
		}
	}

	/**
	 * Verifies that method was called exactly $times times
	 *
	 * $loader->expects($this->exactly(2))
	 * 	->method('view')
	 * 	->withConsecutive(
	 *		['shop_confirm', $this->anything(), TRUE],
	 * 		['shop_tmpl_checkout', $this->anything()]
	 * 	);
	 *
	 *  will be
	 *
	 * $this->verifyInvokedMultipleTimes(
	 * 	$loader,
	 * 	'view',
	 * 	2,
	 * 	[
	 * 		['shop_confirm', $this->anything(), TRUE],
	 * 		['shop_tmpl_checkout', $this->anything()]
	 * 	]
	 * );
	 *
	 * @param object $mock   PHPUnit mock object
	 * @param string $method
	 * @param int    $times
	 * @param array  $params arguments
	 */
	public function verifyInvokedMultipleTimes($mock, $method, $times, $params = null)
	{
		$this->_verify(
			$mock, $method, $params, $this->exactly($times), 'withConsecutive'
		);
	}

	/**
	 * Verifies a method was invoked at least once
	 *
	 * @param object $mock   PHPUnit mock object
	 * @param string $method
	 * @param array  $params arguments
	 */
	public function verifyInvoked($mock, $method, $params = null)
	{
		$this->_verify(
			$mock, $method, $params, $this->atLeastOnce(), 'with'
		);
	}

	/**
	 * Verifies that method was invoked only once
	 *
	 * @param object $mock   PHPUnit mock object
	 * @param string $method
	 * @param array  $params arguments
	 */
	public function verifyInvokedOnce($mock, $method, $params = null)
	{
		$this->_verify(
			$mock, $method, $params, $this->once(), 'with'
		);
	}

	/**
	 * Verifies that method was not called
	 *
	 * @param object $mock   PHPUnit mock object
	 * @param string $method
	 * @param array  $params arguments
	 */
	public function verifyNeverInvoked($mock, $method, $params = null)
	{
		$this->_verify(
			$mock, $method, $params, $this->never(), 'with'
		);
	}

	public function warningOff()
	{
		$this->_error_reporting = error_reporting(E_ALL & ~E_WARNING);
	}

	public function warningOn()
	{
		error_reporting($this->_error_reporting);
	}

	/**
	 * Get Route including 404 check
	 *
	 * @see core/CodeIgniter.php
	 *
	 * @param CI_Route $RTR Router object
	 * @param CI_URI   $URI URI object
	 * @return array   [class, method, pararms]
	 */
	protected function getRoute($RTR, $URI)
	{
		$e404 = FALSE;
		$class = ucfirst($RTR->class);
		$method = $RTR->method;

		if (empty($class) OR ! file_exists(APPPATH.'controllers/'.$RTR->directory.$class.'.php'))
		{
			$e404 = TRUE;
		}
		else
		{
			require_once(APPPATH.'controllers/'.$RTR->directory.$class.'.php');

			if ( ! class_exists($class, FALSE) OR $method[0] === '_' OR method_exists('CI_Controller', $method))
			{
				$e404 = TRUE;
			}
			elseif (method_exists($class, '_remap'))
			{
				$params = array($method, array_slice($URI->rsegments, 2));
				$method = '_remap';
			}
			// WARNING: It appears that there are issues with is_callable() even in PHP 5.2!
			// Furthermore, there are bug reports and feature/change requests related to it
			// that make it unreliable to use in this context. Please, DO NOT change this
			// work-around until a better alternative is available.
			elseif ( ! in_array(strtolower($method), array_map('strtolower', get_class_methods($class)), TRUE))
			{
				$e404 = TRUE;
			}
		}

		if ($e404)
		{
			show_404($RTR->directory.$class.'/'.$method.' is not found');
		}

		if ($method !== '_remap')
		{
			$params = array_slice($URI->rsegments, 2);
		}

		return [$class, $method, $params];
	}
}
