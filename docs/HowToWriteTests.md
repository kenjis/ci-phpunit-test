# CI PHPUnit Test for CodeIgniter 3.0

## How to Write Tests

### Models

#### Using Database

`tests/models/Inventory_model_test.php`
~~~php
<?php

class Inventory_model_test extends TestCase
{
	public function setUp()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('shop/Inventory_model');
		$this->obj = $this->CI->Inventory_model;
	}

	public function test_get_category_list()
	{
		$expected = [
			1 => 'Book',
			2 => 'CD',
			3 => 'DVD',
		];
		$list = $this->obj->get_category_list();
		foreach ($list as $category) {
			$this->assertEquals($expected[$category->id], $category->name);
		}
	}

	public function test_get_category_name()
	{
		$actual = $this->obj->get_category_name(1);
		$expected = 'Book';
		$this->assertEquals($expected, $actual);
	}
}
~~~

Test case class extends `TestCase`.

Don't forget to write `parent::setUpBeforeClass();` if you override `setUpBeforeClass()` method.

See https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/master/application/tests/models/Category_model_test.php

#### Database Seeding

I put [Seeder Library](../application/libraries/Seeder.php) and a sample [Seeder File](../application/database/seeds/CategorySeeder.php).

They are not installed, so if you want to use, copy them manually.

You can use them like below:

~~~php
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		$CI =& get_instance();
		$CI->load->library('Seeder');
		$CI->seeder->call('CategorySeeder');
	}
~~~

See https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/master/application/tests/models/Category_model_test.php

#### Using PHPUnit Mock Objects

You can use `$this->getMockBuilder()`method  in PHPUnit and use `$this->verifyInvoked*()` methods in *CI PHPUnit Test*.

~~~php
	public function setUp()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('Category_model');
		$this->obj = $this->CI->Category_model;
	}

	public function test_get_category_list()
	{
		// Create mock objects for CI_DB_pdo_result and CI_DB_pdo_sqlite_driver
		$return = [
			0 => (object) ['id' => '1', 'name' => 'Book'],
			1 => (object) ['id' => '2', 'name' => 'CD'],
			2 => (object) ['id' => '3', 'name' => 'DVD'],
		];
		$db_result = $this->getMockBuilder('CI_DB_pdo_result')
			->disableOriginalConstructor()
			->getMock();
		$db_result->method('result')->willReturn($return);
		$db = $this->getMockBuilder('CI_DB_pdo_sqlite_driver')
			->disableOriginalConstructor()
			->getMock();
		$db->method('get')->willReturn($db_result);
		
		// Verify invocations
		$this->verifyInvokedOnce(
			$db_result,
			'result',
			[]
		);
		$this->verifyInvokedOnce(
			$db,
			'order_by',
			['id']
		);
		$this->verifyInvokedOnce(
			$db,
			'get',
			['category']
		);

		// Replace property db with mock object
		$this->obj->db = $db;

		$expected = [
			1 => 'Book',
			2 => 'CD',
			3 => 'DVD',
		];
		$list = $this->obj->get_category_list();
		foreach ($list as $category) {
			$this->assertEquals($expected[$category->id], $category->name);
		}

		// Reset CI object for next test case, unless property db won't work
		reset_instance();
		new CI_Controller();
	}
~~~

See https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/master/application/tests/models/Category_model_mocking_db_test.php

### Controllers

#### Request to Controller

You can use `$this->request()` method in *CI PHPUnit Test*.

`tests/controllers/Welcome_test.php`
~~~php
<?php

class Welcome_test extends TestCase
{
	public function test_index()
	{
		$output = $this->request('GET', ['Welcome', 'index']);
		$this->assertContains('<title>Welcome to CodeIgniter</title>', $output);
	}
}
~~~

[TestCase](FunctionAndClassReference.md#class-testcase) class has `$this->request()` method.

See https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/master/application/tests/controllers/Welcome_test.php

#### Request to URI string

~~~php
	public function test_uri_sub_sub_index()
	{
		$output = $this->request('GET', 'sub/sub/index');
		$this->assertContains('<title>Page Title</title>', $output);
	}
~~~

See https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/master/application/tests/controllers/sub/Sub_test.php

#### Ajax Request

You can use `$this->ajaxRequest()` method in *CI PHPUnit Test*.

~~~php
	public function test_index_ajax_call()
	{
		$output = $this->ajaxRequest('GET', ['Ajax', 'index']);
		$expected = '{"name": "John Smith", "age": 33}';
		$this->assertEquals($expected, $output);
	}
~~~

See https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/master/application/tests/controllers/Ajax_test.php

#### Controller with Name Collision

If you have two controllers with the exact same name, PHP Fatal error stops PHPUnit testing.

In this case, you can use PHPUnit annotations `@runInSeparateProcess` and `@preserveGlobalState disabled`. But tests in a separate PHP process is very slow.

~~~php
<?php

class sub_Welcome_test extends TestCase
{
	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_uri_sub_welcome_index()
	{
		$output = $this->request('GET', 'sub/welcome/index');
		$this->assertContains('<title>Page Title</title>', $output);
	}
}
~~~

See https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/master/application/tests/controllers/sub/Welcome_test.php

### `show_error()` and `show_404()`

`show_error()` and `show_404()` in *CI PHPUnit Test* throws `PHPUnit_Framework_Exception`.

~~~php
	/**
	* @expectedException		PHPUnit_Framework_Exception
	* @expectedExceptionCode	404
	*/
	public function test_index()
	{
		$output = $this->request('GET', ['nocontroller', 'noaction']);
	}
~~~

See https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/master/application/tests/controllers/Nocontroller_test.php

### Mock Libraries

You can put mock libraries in `tests/mocks/libraries` folder. You can see [application/tests/mocks/libraries/email.php](../application/tests/mocks/libraries/email.php) as a sample.

With mock libraries, you could replace your object in CodeIgniter instance.

This is how to replace Email library with `Mock_Libraries_Email` class.

~~~php
	public function setUp()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('Mail_model');
		$this->obj = $this->CI->Mail_model;
		$this->CI->email = new Mock_Libraries_Email();
	}
~~~

Mock library class name must be `Mock_Libraries_*`, and it is autoloaded.

### More Samples

Want to see more tests?

* https://github.com/kenjis/codeigniter-tettei-apps/tree/develop/application/tests
* https://github.com/kenjis/ci-app-for-ci-phpunit-test/tree/master/application/tests
