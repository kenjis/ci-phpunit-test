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
 * @property CIPHPUnitTestRequest    $request
 * @property CIPHPUnitTestDouble     $double
 * @property CIPHPUnitTestReflection $reflection
 */
class CIPHPUnitTestCase extends PHPUnit_Framework_TestCase
{
	protected $_error_reporting = -1;
	
	/**
	 * @var CI_Controller CodeIgniter instance
	 */
	protected $CI;
	
	protected $class_map = [
		'request'    => 'CIPHPUnitTestRequest',
		'double'     => 'CIPHPUnitTestDouble',
		'reflection' => 'CIPHPUnitTestReflection',
	];

	public function setCI(CI_Controller $CI)
	{
		$this->CI = $CI;
	}

	public function __get($name)
	{
		if (isset($this->class_map[$name]))
		{
			$this->$name = new $this->class_map[$name]($this);
			return $this->$name;
		}

		throw new LogicException('No such property: ' . $name);
	}

	public static function setUpBeforeClass()
	{
		// Fix CLI args, because you may set invalid URI characters
		// For example, when you run tests on NetBeans
		$_SERVER['argv'] = [
			'index.php',
		];
		$_SERVER['argc'] = 1;
	}

	/**
	 * Reset CodeIgniter instance and assign new CodeIgniter instance as $this->CI
	 */
	public function resetInstance()
	{
		reset_instance();
		if (! CIPHPUnitTest::wiredesignzHmvcInstalled())
		{
			new CI_Controller();
		}
		else
		{
			new CI();
			new MX_Controller();
		}
		$this->CI =& get_instance();
	}

	protected function tearDown()
	{
		if (class_exists('MonkeyPatch', false))
		{
			if (MonkeyPatchManager::isEnabled('FunctionPatcher'))
			{
				try {
					MonkeyPatch::verifyFunctionInvocations();
				} catch (Exception $e) {
					MonkeyPatch::resetFunctions();
					throw $e;
				}

				MonkeyPatch::resetFunctions();
			}

			if (MonkeyPatchManager::isEnabled('MethodPatcher'))
			{
				try {
					MonkeyPatch::verifyMethodInvocations();
				} catch (Exception $e) {
					MonkeyPatch::resetMethods();
					throw $e;
				}

				MonkeyPatch::resetMethods();
			}
		}
	}

	/**
	 * Request to Controller
	 *
	 * @param string       $http_method HTTP method
	 * @param array|string $argv        array of controller,method,arg|uri
	 * @param array        $params      POST parameters/Query string
	 */
	public function request($http_method, $argv, $params = [])
	{
		return $this->request->request($http_method, $argv, $params);
	}

	/**
	 * Request to Controller using ajax request
	 *
	 * @param string       $http_method HTTP method
	 * @param array|string $argv        array of controller,method,arg|uri
	 * @param array        $params      POST parameters/Query string
	 */
	public function ajaxRequest($http_method, $argv, $params = [])
	{
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
		return $this->request($http_method, $argv, $params);
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
	 * @param  array  $params             [method_name => return_value]
	 * @param  bool   $enable_constructor enable constructor or not
	 * @return object PHPUnit mock object
	 */
	public function getDouble($classname, $params, $enable_constructor = false)
	{
		return $this->double->getDouble($classname, $params, $enable_constructor);
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
		$this->double->verifyInvokedMultipleTimes(
			$mock, $method, $times, $params
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
		$this->double->verifyInvoked($mock, $method, $params);
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
		$this->double->verifyInvokedOnce($mock, $method, $params);
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
		$this->double->verifyNeverInvoked($mock, $method, $params);
	}

	public function warningOff()
	{
		$this->_error_reporting = error_reporting(
			E_ALL & ~E_WARNING & ~E_NOTICE
		);
	}

	public function warningOn()
	{
		error_reporting($this->_error_reporting);
	}

	/**
	 * Asserts HTTP response code
	 * 
	 * @param int $code
	 */
	public function assertResponseCode($code)
	{
		$status = $this->request->getStatus();
		$actual = $status['code'];

		$this->assertSame(
			$code,
			$actual,
			'Status code is not ' . $code . ' but ' . $actual . '.'
		);
	}

	/**
	 * Asserts HTTP response header
	 * 
	 * @param string $name  header name
	 * @param string $value header value
	 */
	public function assertResponseHeader($name, $value)
	{
		$CI =& get_instance();
		$actual = $CI->output->get_header($name);

		if ($actual === null)
		{
			$this->fail("The '$name' header is not set.\nNote that `assertResponseHeader()` can only assert headers set by `\$this->output->set_header()`");
		}

		$this->assertEquals(
			$value,
			$actual,
			"The '$name' header is not '$value' but '$actual'."
		);
	}

	/**
	 * Asserts Redirect
	 * 
	 * @param string $uri  URI to redirect
	 * @param int    $code response code
	 */
	public function assertRedirect($uri, $code = null)
	{
		$status = $this->request->getStatus();

		if ($status['redirect'] === null)
		{
			$this->fail('redirect() is not called.');
		}

		if (! function_exists('site_url'))
		{
			$CI =& get_instance();
			$CI->load->helper('url');
		}
		$absolute_url = site_url($uri);
		$expected = 'Redirect to ' . $absolute_url;

		$this->assertSame(
			$expected,
			$status['redirect'],
			'URL to redirect is not ' . $expected . ' but ' . $status['redirect'] . '.'
		);

		if ($code !== null)
		{
			$this->assertSame(
				$code,
				$status['code'],
				'Status code is not ' . $code . ' but ' . $status['code'] . '.'
			);
		}
	}
}
