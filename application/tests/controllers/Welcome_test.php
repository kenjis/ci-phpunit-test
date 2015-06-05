<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Welcome_test extends TestCase
{
	public function test_index()
	{
		$output = $this->request('GET', ['welcome', 'index']);
		$this->assertContains('<title>Welcome to CodeIgniter</title>', $output);
	}

	/**
	 * @expectedException		PHPUnit_Framework_Exception
	 * @expectedExceptionCode	404
	 */
	public function test_method_404()
	{
		$output = $this->request('GET', ['welcome', 'method_not_exist']);
	}

	public function test_APPPATH()
	{
		$actual = realpath(APPPATH);
		$expected = realpath(__DIR__ . '/../..');
		$this->assertEquals(
			$expected,
			$actual,
			'Your APPPATH seems to be wrong. Check your $application_folder in tests/Bootstrap.php'
		);
	}
}
