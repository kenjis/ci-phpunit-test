<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

use PHPUnit\Framework\TestCase;

class CIPHPUnitTestDouble
{
	protected $testCase;

	public function __construct(TestCase $testCase)
	{
		$this->testCase = $testCase;
	}

	/**
	 * Get Mock Object
	 *
	 * $email = $this->getMockBuilder('CI_Email')
	 *	->disableOriginalConstructor()
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
	 * @param  mixed  $constructor_params false: disable constructor, array: constructor params
	 *
	 * @return mixed PHPUnit mock object
	 */
	public function getDouble($classname, $params, $constructor_params = false)
	{
		// `disableOriginalConstructor()` is the default, because if we call
		// constructor, it may call `$this->load->...` or other CodeIgniter
		// methods in it. But we can't use them in
		// `$this->request->setCallablePreConstructor()`
		$mockBuilder = $this->testCase->getMockBuilder($classname);
		if ($constructor_params === false)
		{
			$mockBuilder->disableOriginalConstructor();
		}
		elseif (is_array($constructor_params))
		{
			$mockBuilder->setConstructorArgs($constructor_params);
		}

		$methods = [];
		$onConsecutiveCalls = [];
		$otherCalls = [];

		foreach ($params as $key => $val) {
			if (is_int($key)) {
				$onConsecutiveCalls = array_merge($onConsecutiveCalls, $val);
				$methods[] = array_keys($val)[0];
			} else {
				$otherCalls[$key] = $val;
				$methods[] = $key;
			}
		}

		$mock = $mockBuilder->setMethods($methods)->getMock();

		foreach ($onConsecutiveCalls as $method => $returns) {
			$mock->expects($this->testCase->any())->method($method)
				->will(
					call_user_func_array(
						[$this->testCase, 'onConsecutiveCalls'],
						$returns
					)
				);
		}

		foreach ($otherCalls as $method => $return)
		{
			if (is_object($return) && ($return instanceof PHPUnit_Framework_MockObject_Stub || $return instanceof PHPUnit\Framework\MockObject\Stub)) {
				$mock->expects($this->testCase->any())->method($method)
					->will($return);
			} elseif (is_object($return) && $return instanceof Closure) {
				$mock->expects($this->testCase->any())->method($method)
					->willReturnCallback($return);
			} elseif ($return === ':void') {
				$mock->expects($this->testCase->any())->method($method);
			} else {
				$mock->expects($this->testCase->any())->method($method)
					->willReturn($return);
			}
		}

		return $mock;
	}

	protected function _verify($mock, $method, $params, $expects, $with)
	{
		$invocation = $mock->expects($expects)
			->method($method);

		if ($params === null) {
			return;
		}

		call_user_func_array([$invocation, $with], $params);
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
	 * @param mixed  $mock   PHPUnit mock object
	 * @param string $method
	 * @param int    $times
	 * @param array  $params arguments
	 */
	public function verifyInvokedMultipleTimes($mock, $method, $times, $params = null)
	{
		$this->_verify(
			$mock, $method, $params, $this->testCase->exactly($times), 'withConsecutive'
		);
	}

	/**
	 * Verifies a method was invoked at least once
	 *
	 * @param mixed  $mock   PHPUnit mock object
	 * @param string $method
	 * @param array  $params arguments
	 */
	public function verifyInvoked($mock, $method, $params = null)
	{
		$this->_verify(
			$mock, $method, $params, $this->testCase->atLeastOnce(), 'with'
		);
	}

	/**
	 * Verifies that method was invoked only once
	 *
	 * @param mixed  $mock   PHPUnit mock object
	 * @param string $method
	 * @param array  $params arguments
	 */
	public function verifyInvokedOnce($mock, $method, $params = null)
	{
		$this->_verify(
			$mock, $method, $params, $this->testCase->once(), 'with'
		);
	}

	/**
	 * Verifies that method was not called
	 *
	 * @param mixed  $mock   PHPUnit mock object
	 * @param string $method
	 * @param array  $params arguments
	 */
	public function verifyNeverInvoked($mock, $method, $params = null)
	{
		$this->_verify(
			$mock, $method, $params, $this->testCase->never(), 'with'
		);
	}
}
