# CI PHPUnit Test for CodeIgniter 3.0

version: **master** | 
[v0.4.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/FunctionAndClassReference.md) | 
[v0.3.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.3.0/docs/FunctionAndClassReference.md) | 
[v0.2.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.2.0/docs/FunctionAndClassReference.md)

## Function/Class Reference

- [*function* `reset_instance()`](#function-reset_instance)
- [[Deprecated] *function* `get_new_instance()`](#deprecated-function-get_new_instance)
- [*function* `set_is_cli($return)`](#function-set_is_clireturn)
- [*function* `load_class_instance($classname, $instance)`](#function-load_class_instanceclassname-instance)
- [*class* TestCase](#class-testcase)
	- [`TestCase::request($method, $argv, $params = [], $callable = null)`](#testcaserequestmethod-argv-params---callable--null)
		- [`request->setCallable()`](#request-setcallable)
		- [`request->setCallablePreConstructor()`](#request-setcallablepreconstructor)
		- [`request->enableHooks()`](#request-enablehooks)
	- [`TestCase::ajaxRequest($method, $argv, $params = [], $callable = null)`](#testcaseajaxrequestmethod-argv-params---callable--null)
	- [`TestCase::assertResponseCode($code)`](#testcaseassertresponsecodecode)
	- [`TestCase::assertRedirect($uri, $code = null)`](#testcaseassertredirecturi-code--null)
	- [`TestCase::getDouble($classname, $params)`](#testcasegetdoubleclassname-params)
	- [`TestCase::verifyInvoked($mock, $method, $params)`](#testcaseverifyinvokedmock-method-params)
	- [`TestCase::verifyInvokedOnce($mock, $method, $params)`](#testcaseverifyinvokedoncemock-method-params)
	- [`TestCase::verifyInvokedMultipleTimes($mock, $method, $times, $params)`](#testcaseverifyinvokedmultipletimesmock-method-times-params)
	- [`TestCase::verifyNeverInvoked($mock, $method, $params)`](#testcaseverifyneverinvokedmock-method-params)
	- [`TestCase::warningOff()`](#testcasewarningoff)
	- [`TestCase::warningOn()`](#testcasewarningon)

### *function* `reset_instance()`

Reset CodeIgniter instance. You must create new controller instance after calling this function.

### [Deprecated] *function* `get_new_instance()`

`returns` CI_Controller instance

Generate new CodeIgniter instance and get it.

This function is deprecated. Please use `reset_instance()` instead.

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

### *function* `set_is_cli($return)`

| param   | type | description         |
|---------|------|---------------------|
|`$return`| bool | return value to set |

Set return value of `is_cli()` function.

~~~php
set_is_cli(FALSE);
~~~

### *function* `load_class_instance($classname, $instance)`

| param      | type   | description     |
|------------|--------|-----------------|
|`$classname`| string | class name      |
|`$instance` | object | object instance |

Inject an instance directly into `load_class()` function.

~~~php
$email = $this->getMockBuilder('CI_Email')
	->setMethods(['send'])
	->getMock();
$email->method('send')
	->willReturn(TRUE);
load_class_instance('email', $email);
~~~

### *class* TestCase

#### `TestCase::request($method, $argv, $params = [], $callable = null)`

| param     | type         | description                                    |
|-----------|--------------|------------------------------------------------|
|`$method`  | string       | HTTP method                                    |
|`$argv`    | array/string | controller, method [, arg1, ...] / URI string  |
|`$params`  | array        | POST parameters / Query string                 |
|`$callable`| callable     | **[Deprecated]** function to run after controller instantiation |

`returns` (string) output strings (view)

Run a controller method or make a request to URI string after `reset_instance()`.

~~~php
$output = $this->request('GET', ['Form', 'index']);
~~~

~~~php
$output = $this->request('GET', 'products/shoes/show/123');
~~~

##### `request->setCallable()`

Set function to run after controller instantiation.

4th param `$callable` of `$this->request()` method is deprecated. Use `$this->request->setCallable()` method instead.

*before:*
~~~php
$load_agent = function ($CI) {
	$CI->load->library('user_agent');
};
$output = $this->request('GET', ['Bbs', 'index'], [], $load_agent);
~~~

↓

*after:*
~~~php
$this->request->setCallable(
	function ($CI) {
		$CI->load->library('user_agent');
	};
);
$output = $this->request('GET', ['Bbs', 'index']);
~~~

##### `request->setCallablePreConstructor()`

Set function to run before controller instantiation.

~~~php
$this->request->setCallablePreConstructor(
	function () {
		// Get mock object
		$auth = $this->getDouble(
			'Ion_auth', ['logged_in' => TRUE]
		);
		// Inject mock object
		load_class_instance('ion_auth', $auth);
	}
);
~~~

##### `request->enableHooks()`

If you want to enable hooks, call `$this->request->enableHooks()` method. It enables only `pre_controller`, `post_controller_constructor`, `post_controller` hooks.

~~~php
$this->request->enableHooks();
$output = $this->request('GET', 'products/shoes/show/123');
~~~

#### `TestCase::ajaxRequest($method, $argv, $params = [], $callable = null)`

| param     | type         | description                                    |
|-----------|--------------|------------------------------------------------|
|`$method`  | string       | HTTP method                                    |
|`$argv`    | array/string | controller, method [, arg1, ...] / URI string  |
|`$params`  | array        | POST parameters / Query string                 |
|`$callable`| callable     | **[Deprecated]** function to run after controller instantiation |

`returns` (string) output strings

The same as `TestCase::request()`, but this makes a Ajax request. This adds only `$_SERVER['HTTP_X_REQUESTED_WITH']`.

#### `TestCase::assertResponseCode($code)`

| param   | type | description      |
|---------|------|------------------|
|`$code`  | int  | HTTP status code |

Check for a specific response code on your controller tests.

#### `TestCase::assertRedirect($uri, $code = null)`

| param   | type   | description      |
|---------|--------|------------------|
|`$uri`   | string | URI to redirect  |
|`$code`  | int    | HTTP status code |

Check if `redirect()` is called on your controller tests.

#### `TestCase::getDouble($classname, $params)`

| param      | type    | description                   |
|------------|---------|-------------------------------|
|`$classname`| string  | class name                    |
|`$params`   | array   | [method_name => return_value] |

`returns` (object) PHPUnit mock object

Get PHPUnit mock object.

~~~php
$email = $this->getMockBuilder('CI_Email')
	->setMethods(['send'])
	->getMock();
$email->method('send')
	->willReturn(TRUE);
~~~

You could write code above like below:

~~~php
$email = $this->getDouble('CI_Email', ['send' => TRUE]);
~~~

#### `TestCase::verifyInvoked($mock, $method, $params)`

| param   | type   | description         |
|---------|--------|---------------------|
|`$mock`  | object | PHPUnit mock object |
|`$method`| string | method name         |
|`$params`| array  | arguments           |

Verifies a method was invoked at least once.

~~~php
$loader->expects($this->atLeastOnce())
	->method('view')
	->with(
		['shop_confirm', $this->anything(), TRUE]
	);
~~~

You could write code above like below:

~~~php
$this->verifyInvoked(
	$loader,
	'view',
	[
		['shop_confirm', $this->anything(), TRUE]
	]
);
~~~

#### `TestCase::verifyInvokedOnce($mock, $method, $params)`

| param   | type   | description         |
|---------|--------|---------------------|
|`$mock`  | object | PHPUnit mock object |
|`$method`| string | method name         |
|`$params`| array  | arguments           |

Verifies that method was invoked only once.

~~~php
$loader->expects($this->once())
	->method('view')
	->with(
		['shop_confirm', $this->anything(), TRUE]
	);
~~~

You could write code above like below:

~~~php
$this->verifyInvokedOnce(
	$loader,
	'view',
	[
		['shop_confirm', $this->anything(), TRUE]
	]
);
~~~

#### `TestCase::verifyInvokedMultipleTimes($mock, $method, $times, $params)`

| param   | type   | description         |
|---------|--------|---------------------|
|`$mock`  | object | PHPUnit mock object |
|`$method`| string | method name         |
|`$times` | int    | times               |
|`$params`| array  | arguments           |

Verifies that method was called exactly $times times.

~~~php
$loader->expects($this->exactly(2))
	->method('view')
	->withConsecutive(
		['shop_confirm', $this->anything(), TRUE],
		['shop_tmpl_checkout', $this->anything()]
	);
~~~

You could write code above like below:

~~~php
$this->verifyInvokedMultipleTimes(
	$loader,
	'view',
	2,
	[
		['shop_confirm', $this->anything(), TRUE],
		['shop_tmpl_checkout', $this->anything()]
	]
);
~~~

#### `TestCase::verifyNeverInvoked($mock, $method, $params)`

| param   | type   | description         |
|---------|--------|---------------------|
|`$mock`  | object | PHPUnit mock object |
|`$method`| string | method name         |
|`$params`| array  | arguments           |

Verifies that method was not called.

~~~php
$loader->expects($this->never())
	->method('view')
	->with(
		['shop_confirm', $this->anything(), TRUE]
	);
~~~

You could write code above like below:

~~~php
$this->verifyNeverInvoked(
	$loader,
	'view',
	[
		['shop_confirm', $this->anything(), TRUE]
	]
);
~~~

#### `TestCase::warningOff()`

Turn off WARNING in error reporting.

#### `TestCase::warningOn()`

Restore error reporting.
