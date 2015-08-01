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
	
	/**
	 * @var CIPHPUnitTestRequest
	 */
	protected $request;
	
	/**
	 * @var CIPHPUnitTestDouble
	 */
	protected $double;

	/**
	 * Constructs a test case with the given name.
	 *
	 * @param string $name
	 * @param array  $data
	 * @param string $dataName
	 */
	public function __construct($name = null, array $data = array(), $dataName = '')
	{
		parent::__construct($name, $data, $dataName);

		$this->request = new CIPHPUnitTestRequest();
		$this->double = new CIPHPUnitTestDouble($this);
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

	public function tearDown()
	{
		if (class_exists('MonkeyPatch', false))
		{
			if (MonkeyPatchManager::isEnabled('FunctionPatcher'))
			{
				MonkeyPatch::resetFunctions();
			}

			try {
				if (MonkeyPatchManager::isEnabled('MethodPatcher'))
				{
					MonkeyPatch::verifyInvocations();
				}
			} catch (Exception $e) {
				if (MonkeyPatchManager::isEnabled('MethodPatcher'))
				{
					MonkeyPatch::resetMethods();
				}

				throw $e;
			}

			if (MonkeyPatchManager::isEnabled('MethodPatcher'))
			{
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
	 * @param callable     $callable    [deprecated] function to run after controller instantiation. Use $this->request->setCallable() method instead
	 */
	public function request($http_method, $argv, $params = [], $callable = null)
	{
		return $this->request->request($http_method, $argv, $params, $callable);
	}

	/**
	 * Request to Controller using ajax request
	 *
	 * @param string       $http_method HTTP method
	 * @param array|string $argv        array of controller,method,arg|uri
	 * @param array        $params      POST parameters/Query string
	 * @param callable     $callable    [deprecated] function to run after controller instantiation. Use $this->request->setCallable() method instead
	 */
	public function ajaxRequest($http_method, $argv, $params = [], $callable = null)
	{
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
		return $this->request($http_method, $argv, $params, $callable);
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
		return $this->double->getDouble($classname, $params);
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
		$this->_error_reporting = error_reporting(E_ALL & ~E_WARNING);
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
	 * Set Expected Redirect
	 * 
	 * This method needs <https://github.com/kenjis/ci-phpunit-test/blob/master/application/helpers/MY_url_helper.php>.
	 * 
	 * @param string $uri  URI to redirect
	 * @param int    $code Response Code
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
