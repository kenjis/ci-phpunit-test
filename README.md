# CI PHPUnit Test for CodeIgniter 3.0

[![Latest Stable Version](https://poser.pugx.org/kenjis/ci-phpunit-test/v/stable)](https://packagist.org/packages/kenjis/ci-phpunit-test) [![Total Downloads](https://poser.pugx.org/kenjis/ci-phpunit-test/downloads)](https://packagist.org/packages/kenjis/ci-phpunit-test) [![Latest Unstable Version](https://poser.pugx.org/kenjis/ci-phpunit-test/v/unstable)](https://packagist.org/packages/kenjis/ci-phpunit-test) [![License](https://poser.pugx.org/kenjis/ci-phpunit-test/license)](https://packagist.org/packages/kenjis/ci-phpunit-test)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kenjis/ci-phpunit-test/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kenjis/ci-phpunit-test/?branch=master)
[![Coverage Status](https://coveralls.io/repos/kenjis/ci-phpunit-test/badge.svg?branch=master)](https://coveralls.io/r/kenjis/ci-phpunit-test?branch=master)
[![Build Status](https://travis-ci.org/kenjis/ci-phpunit-test.svg?branch=master)](https://travis-ci.org/kenjis/ci-phpunit-test)

An easier way to use PHPUnit with [CodeIgniter](https://github.com/bcit-ci/CodeIgniter) 3.0.

You don't have to modify CodeIgniter core files at all!

![Screenshot: Running tests on NetBeans](http://forum.codeigniter.com/attachment.php?aid=210)

## Requirements

* PHP 5.4.0 or later
* CodeIgniter 3.0.*
* PHPUnit

## Folder Structure

~~~
codeigniter/
├── application/
│   └── tests/
│        ├── _ci_phpunit_test/ ... don't touch! files CI PHPUnit Test uses
│        ├── Bootstrap.php     ... bootstrap file for PHPUnit
│        ├── TestCase.php      ... TestCase class
│        ├── controllers/      ... put your controller tests
│        ├── mocks/
│        │   └── libraries/    ... mock libraries
│        ├── models/           ... put your model tests
│        └── phpunit.xml       ... config file for PHPUnit
└── vendor/
~~~

## Installation

### Step 1

Download `ci-phpunit-test`: https://github.com/kenjis/ci-phpunit-test/archive/master.zip

Unzip and copy `application/tests` folder into your `application` folder in CodeIgniter project. That's it. Go to Step 2.

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

	$application_folder = '../../application';

	define('FCPATH', realpath(dirname(__FILE__).'/../../public').'/');
~~~

If you install CodeIgniter using [codeigniter-composer-installer](https://github.com/kenjis/codeigniter-composer-installer), you don't have to.

## Upgrading

Download latest `ci-phpunit-test`: https://github.com/kenjis/ci-phpunit-test/archive/master.zip

Unzip and replace `application/tests/_ci_phpunit_test` folder.

If you like Composer:

~~~
$ cd /path/to/codeigniter/
$ composer update kenjis/ci-phpunit-test
$ php vendor/kenjis/ci-phpunit-test/update.php
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

Don't forget `parent::setUpBeforeClass();` if you override `setUpBeforeClass()` method.

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

### More Samples

Want to see more tests?

* https://github.com/kenjis/codeigniter-tettei-apps/tree/develop/application/tests
* https://github.com/kenjis/ci-app-for-ci-phpunit-test/tree/master/application/tests

## Can and Can't

*CI PHPUnit Test* does not want to modify CodeIgniter core files. The more you modify core, the more you get difficulities when you update CodeIgniter.

In fact, it uses a modified class and a few functions. But I try to modify as little as possible.

The functions and the class which are modified:

* function `load_class()`
* function `is_loaded()`
* function `is_cli()`
* function `show_error()`
* function `show_404()`
* class `CI_Loader`

They are in `tests/_ci_phpunit_test/replacing` folder.

### MY_Loader

*CI PHPUnit Test* replaces `CI_Loader` and modifies below methods:

* `CI_Loader::model()`
* `CI_Loader::_ci_load_library()`
* `CI_Loader::_ci_load_stock_library()`

But if you place MY_Loader, your MY_Loader extends the loader of *CI PHPUnit Test*.

If your MY_Loader overrides the above methods, probably *CI PHPUnit Test* does not work correctly.

### exit()

*CI PHPUnit Test* does not care functions/classes which `exit()` or `die()` (Except for `show_error()` and `show_404()`).

So, for example, if you use URL helper `redirect()` in your application code, your testing ends with it.

To aviod it, you can modify `redirect()` in your application. I put a sample [MY_url_helper.php](application/helpers/MY_url_helper.php). (I think CodeIgniter code itself should be changed testable.)

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

When you skip `exit()`, if there are code after it (maybe in your controllers), it will run. You should make sure no code runs.

### Reset CodeIgniter object

CodeIgniter has a function `get_instance()` to get the CodeIgniter object (CodeIgniter instance or CodeIgniter super object).

*CI PHPUnit Test* has a new function `reset_instance()` which reset the current CodeIgniter object. After resetting, you can create a new your Controller instance with new state.

You can see how to use it in [application/tests/_ci_phpunit_test/CIPHPUnitTestCase.php](application/tests/_ci_phpunit_test/CIPHPUnitTestCase.php).

#### [Deprecated] `get_new_instance()`

A function `get_new_instance()` is deprecated. Please use `reset_instance()` instead.

*before:*
~~~php
$this->CI = get_new_instance();
$controller = new Welcome();
~~~

↓

*after:*
~~~php
reset_instance();
$controller = new Welcome();
$this->CI =& get_instance();
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

## Function/Class Reference

See [docs/FunctionAndClassReference.md](docs/FunctionAndClassReference.md)

## Related Projects for CodeIgniter 3.0

* [CodeIgniter Composer Installer](https://github.com/kenjis/codeigniter-composer-installer)
* [Cli for CodeIgniter 3.0](https://github.com/kenjis/codeigniter-cli)
* [CodeIgniter Simple and Secure Twig](https://github.com/kenjis/codeigniter-ss-twig)
* [CodeIgniter Doctrine](https://github.com/kenjis/codeigniter-doctrine)
* [CodeIgniter Deployer](https://github.com/kenjis/codeigniter-deployer)
