# CI PHPUnit Test for CodeIgniter 3.0

An easier way to use PHPUnit with [CodeIgniter](https://github.com/bcit-ci/CodeIgniter) 3.0.

You don't have to modify CodeIgniter core files at all!

## Requirements

* PHP 5.4.0 or later
* PHPUnit

## Folder Structure

~~~
codeigniter/
├── application/
│   └── tests/
│        ├── Bootstrap.php   ... bootstrap file for PHPUnit
│        ├── TestCase.php    ... TestCase class
│        ├── controllers/    ... put your controller tests
│        ├── mocks/
│        │   └── libraries/  ... mock libraries
│        ├── models/         ... put your model tests
│        ├── phpunit.xml     ... config file for PHPUnit
│        └── replace/        ... don't edit! files CI PHPUnit Test uses
└── vendor/
~~~

## Installation

### Step 1

Download `ci-phpunit-test`: https://github.com/kenjis/ci-phpunit-test/archive/master.zip

Unzip and Copy `application/tests` folder into your `application` folder in CodeIgniter project. That's it. Go to Step 2.

If you like Composer:

~~~
$ cd /path/to/codeigniter/
$ composer require kenjis/ci-phpunit-test:1.0.x@dev --dev
~~~

And run `install.php`:

~~~
$ php vendor/kenjis/ci-phpunit-test/install.php
~~~

* Above command always overwrites exisiting files.
* You must run it at CodeIgniter project root folder.

### Step 2

Fix the paths in `tests/Bootstrap.php` if you need.

~~~php
	$system_path = '../../vendor/codeigniter/framework/system';

	$application_folder = '../application';

	define('FCPATH', realpath(dirname(__FILE__).'/../../public').'/');
~~~

## How to Run Tests

You have to install PHPUnit before running tests.

~~~
$ cd /path/to/codeigniter/
$ cd application/tests/
$ phpunit
PHPUnit 4.1.6 by Sebastian Bergmann.

Configuration read from /.../codeigniter/application/tests/phpunit.xml

.

Time: 470 ms, Memory: 3.50Mb

OK (1 test, 1 assertion)

Generating code coverage report in Clover XML format ... done

Generating code coverage report in HTML format ... done
~~~

To generate coverage report, Xdebug is needed.

## How to Write Tests

### Models

`tests/models/Inventory_model_test.php`
~~~php
<?php

class Inventory_model_test extends PHPUnit_Framework_TestCase
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

### Database Seeding

I put [Seeder Library](application/libraries/Seeder.php) and a sample [Seeder File](application/database/seeds/CategorySeeder.php).

They are not installed, so if you want to use, copy them manually.

You can use them like below:

~~~php
	public static function setUpBeforeClass()
	{
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
		$output = $this->request('GET', ['welcome', 'index']);
		$this->assertContains('<title>Welcome to CodeIgniter</title>', $output);
	}
}
~~~

You can use `$this->request()` method if you extend `TestCase`.

### More Samples

Want to see more tests?

* https://github.com/kenjis/codeigniter-tettei-apps/tree/develop/application/tests

## Can and Can't

*CI PHPUnit Test* does not want to modify CodeIgniter core files. The more you modify core, the more you get difficulities when you update CodeIgniter.

In fact, it uses a modified class and functions. But I try to modify as less as possible.

The functions and the class which are modified:

* function `load_class()`
* function `is_loaded()`
* class `CI_Loader`

### exit()

*CI PHPUnit Test* does not care functions/classes which `exit()` or `die()`. So if you use URL helper `redirect()` in your application code, your testing ends with it.

To aviod it, you can modify `redirect()` in your application. (I think CodeIgniter code itself should be changed testable.)

*before:*
~~~php
exit;
~~~

↓

*after:*
~~~php
if (ENVIRONMENT !== 'testing')
{
	exit;
}
~~~

### Getting new CodeIgniter object

CodeIgniter has a function `get_instance()` to get the CodeIgniter object (CodeIgniter instance or CodeIgniter super object).

*CI PHPUnit Test* has a new function `get_new_instance()` which instantiates new CodeIgniter object. To use it, you could run tests with new state.

You can see how to use it in [application/tests/TestCase.php](application/tests/TestCase.php).

### Mock Libraries

You can put mock libraries in `tests/mocks/libraries` folder. You can see [application/tests/mocks/libraries/email.php](application/tests/mocks/libraries/email.php) as a sample.

With mock libraries, you could replace your object in CodeIgniter instance.

This is how to replace Email library with `Mock_Libraries_Email` class.

~~~php
	public function setUp()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('shop/Mail_model');
		$this->obj = $this->CI->Mail_model;
		$this->CI->email = new Mock_Libraries_Email();
	}
~~~

Mock library classname must be `Mock_Libraries_*`, and it is autoloaded.

## Related

If you want to install CodeIgniter via Composer, check it.

* https://github.com/kenjis/codeigniter-composer-installer

If you want a commnad line tool, check it.

* https://github.com/kenjis/codeigniter-cli
