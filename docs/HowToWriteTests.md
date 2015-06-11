# CI PHPUnit Test for CodeIgniter 3.0

## How to Write Tests

### Models

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

### Database Seeding

I put [Seeder Library](application/libraries/Seeder.php) and a sample [Seeder File](application/database/seeds/CategorySeeder.php).

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

### Controllers

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

[TestCase](docs/FunctionAndClassReference.md#class-testcase) class has `$this->request()` method.

### `show_error()` and `show_404()`

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

### Mock Libraries

You can put mock libraries in `tests/mocks/libraries` folder. You can see [application/tests/mocks/libraries/email.php](application/tests/mocks/libraries/email.php) as a sample.

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
