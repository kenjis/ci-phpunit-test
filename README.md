# CI PHPUnit Test for CodeIgniter 3.0

[![Latest Stable Version](https://poser.pugx.org/kenjis/ci-phpunit-test/v/stable)](https://packagist.org/packages/kenjis/ci-phpunit-test) [![Total Downloads](https://poser.pugx.org/kenjis/ci-phpunit-test/downloads)](https://packagist.org/packages/kenjis/ci-phpunit-test) [![Latest Unstable Version](https://poser.pugx.org/kenjis/ci-phpunit-test/v/unstable)](https://packagist.org/packages/kenjis/ci-phpunit-test) [![License](https://poser.pugx.org/kenjis/ci-phpunit-test/license)](https://packagist.org/packages/kenjis/ci-phpunit-test)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kenjis/ci-phpunit-test/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kenjis/ci-phpunit-test/?branch=master)
[![Coverage Status](https://coveralls.io/repos/kenjis/ci-phpunit-test/badge.svg?branch=master)](https://coveralls.io/r/kenjis/ci-phpunit-test?branch=master)
[![Build Status](https://travis-ci.org/kenjis/ci-phpunit-test.svg?branch=master)](https://travis-ci.org/kenjis/ci-phpunit-test)

An easier way to use PHPUnit with [CodeIgniter](https://github.com/bcit-ci/CodeIgniter) 3.0.

* You don't have to modify CodeIgniter core files at all.
* You can write controller tests easily.
* Well documented.

![Screenshot: Running tests on NetBeans](http://forum.codeigniter.com/attachment.php?aid=210)

## Requirements

* PHP 5.4.0 or later
* CodeIgniter 3.0.*
* PHPUnit

## Change Log

See [Change Log](https://github.com/kenjis/ci-phpunit-test/blob/master/docs/ChangeLog.md).

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

Download latest `ci-phpunit-test`: https://github.com/kenjis/ci-phpunit-test/releases

Unzip and copy `application/tests` folder into your `application` folder in CodeIgniter project. That's it.

If you like Composer:

~~~
$ cd /path/to/codeigniter/
$ composer require kenjis/ci-phpunit-test --dev
~~~

And run `install.php`:

~~~
$ php vendor/kenjis/ci-phpunit-test/install.php
~~~

* Above command always overwrites exisiting files.
* You must run it at CodeIgniter project root folder.

## Upgrading

Download latest `ci-phpunit-test`: https://github.com/kenjis/ci-phpunit-test/releases

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
PHPUnit 4.6.10 by Sebastian Bergmann and contributors.

Configuration read from /.../codeigniter/application/tests/phpunit.xml

...

Time: 635 ms, Memory: 4.50Mb

OK (3 tests, 4 assertions)

Generating code coverage report in Clover XML format ... done

Generating code coverage report in HTML format ... done
~~~

To generate coverage report, Xdebug is needed.

## How to Write Tests

See [How To Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/master/docs/HowToWriteTests.md).

## Can and Can't

*CI PHPUnit Test* does not want to modify CodeIgniter core files. The more you modify core, the more you get difficulities when you update CodeIgniter.

In fact, it uses a modified class and a few functions. But I try to modify as little as possible.

The functions and the class which are modified:

* function `load_class()`
* function `is_loaded()`
* function `is_cli()`
* function `show_error()`
* function `show_404()`
* function `set_status_header()`
* class `CI_Loader`

They are in `tests/_ci_phpunit_test/replacing` folder.

And *CI PHPUnit Test* adds a property dynamically:

* property `CI_Output::_status`

### MY_Loader

*CI PHPUnit Test* replaces `CI_Loader` and modifies below methods:

* `CI_Loader::model()`
* `CI_Loader::_ci_load_library()`
* `CI_Loader::_ci_load_stock_library()`

But if you place MY_Loader, your MY_Loader extends the loader of *CI PHPUnit Test*.

If your MY_Loader overrides the above methods, probably *CI PHPUnit Test* does not work correctly.

### `exit()`

*CI PHPUnit Test* does not care functions/classes which `exit()` or `die()` (Except for [show_error() and show_404()](https://github.com/kenjis/ci-phpunit-test/blob/master/docs/HowToWriteTests.md#show_error-and-show_404)).

So, for example, if you use URL helper `redirect()` in your application code, your testing ends with it.

I recommend you not to use `exit()` or `die()` in your code. And you have to skip `exit()` somehow in CodeIgniter code.

For example, you can modify `redirect()` using `MY_url_helper.php` in your application. I put a sample [MY_url_helper.php](https://github.com/kenjis/ci-phpunit-test/blob/master/application/helpers/MY_url_helper.php). (I think CodeIgniter code itself should be changed testable.)

See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/master/docs/HowToWriteTests.md#redirect) for details.

### Reset CodeIgniter object

CodeIgniter has a function `get_instance()` to get the CodeIgniter object (CodeIgniter instance or CodeIgniter super object).

*CI PHPUnit Test* has a new function `reset_instance()` which reset the current CodeIgniter object. After resetting, you can (and must) create a new your Controller instance with new state.

## Function/Class Reference

See [Function and Class Reference](https://github.com/kenjis/ci-phpunit-test/blob/master/docs/FunctionAndClassReference.md).

## Related Projects for CodeIgniter 3.0

* [CodeIgniter Composer Installer](https://github.com/kenjis/codeigniter-composer-installer)
* [Cli for CodeIgniter 3.0](https://github.com/kenjis/codeigniter-cli)
* [CodeIgniter Simple and Secure Twig](https://github.com/kenjis/codeigniter-ss-twig)
* [CodeIgniter Doctrine](https://github.com/kenjis/codeigniter-doctrine)
* [CodeIgniter Deployer](https://github.com/kenjis/codeigniter-deployer)
* [CodeIgniter3 Filename Checker](https://github.com/kenjis/codeigniter3-filename-checker)
