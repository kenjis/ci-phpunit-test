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
	}

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
	 * @param callable     $callable
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
}
