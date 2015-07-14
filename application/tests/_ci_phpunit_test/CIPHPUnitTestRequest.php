<?php

class CIPHPUnitTestRequest
{
	protected $callable;
	protected $enableHooks = false;

	/**
	 * Set callable
	 * 
	 * @param callable $callable function to run after controller instantiation
	 */
	public function setCallable(callable $callable)
	{
		$this->callable = $callable;
	}

	/**
	 * Enable Hooks for Controllres
	 * This enables only pre_controller, post_controller_constructor, post_controller
	 */
	public function enableHooks()
	{
		$this->enableHooks = true;
	}

	/**
	 * Request to Controller
	 *
	 * @param string       $http_method HTTP method
	 * @param array|string $argv        array of controller,method,arg|uri
	 * @param array        $params      POST parameters/Query string
	 * @param callable     $callable    [deprecated] function to run after controller instantiation. Use setCallable() method instead
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
	 * Call Controller Method
	 *
	 * @param string   $http_method    HTTP method
	 * @param array    $argv           controller, method [, arg1, ...]
	 * @param array    $request_params POST parameters/Query string
	 * @param callable $callable       [deprecated] function to run after controller instantiation. Use setCallable() method instead
	 */
	protected function callControllerMethod($http_method, $argv, $request_params, $callable = null)
	{
		$_SERVER['REQUEST_METHOD'] = $http_method;
		$_SERVER['argv'] = array_merge(['index.php'], $argv);

		if ($http_method === 'POST')
		{
			$_POST = $request_params;
		}
		elseif ($http_method === 'GET')
		{
			$_GET = $request_params;
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

		$params = $argv;

		// @deprecated
		if (is_callable($callable))
		{
			$this->callable = $callable;
		}

		return $this->createAndCallController($class, $method, $params);
	}

	/**
	 * Request to URI
	 *
	 * @param string   $http_method    HTTP method
	 * @param string   $uri            URI string
	 * @param array    $request_params POST parameters/Query string
	 * @param callable $callable       [deprecated] function to run after controller instantiation. Use setCallable() method instead
	 */
	protected function requestUri($http_method, $uri, $request_params, $callable = null)
	{
		$_SERVER['REQUEST_METHOD'] = $http_method;
		$_SERVER['argv'] = ['index.php', $uri];

		if ($http_method === 'POST')
		{
			$_POST = $request_params;
		}
		elseif ($http_method === 'GET')
		{
			$_GET = $request_params;
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

		// @deprecated
		if (is_callable($callable))
		{
			$this->callable = $callable;
		}

		return $this->createAndCallController($class, $method, $params);
	}

	protected function createAndCallController($class, $method, $params)
	{
		ob_start();

		if ($this->enableHooks)
		{
			$EXT =& load_class('Hooks', 'core');
			$EXT->call_hook('pre_controller');
		}

		// Create controller
		$controller = new $class;
		$CI =& get_instance();
		if (is_callable($this->callable))
		{
			$callable = $this->callable;
			$callable($CI);
		}

		if ($this->enableHooks)
		{
			$EXT->call_hook('post_controller_constructor');
		}

		// Call controller method
		call_user_func_array([$controller, $method], $params);
		$output = ob_get_clean();

		if ($output == '')
		{
			$output = $CI->output->get_output();
		}

		if ($this->enableHooks)
		{
			$EXT->call_hook('post_controller');
		}

		return $output;
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
