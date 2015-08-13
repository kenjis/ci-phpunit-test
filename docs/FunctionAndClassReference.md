# CI PHPUnit Test for CodeIgniter 3.0

version: **v0.6.2** | 
[v0.5.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.5.0/docs/FunctionAndClassReference.md) | 
[v0.4.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/FunctionAndClassReference.md) | 
[v0.3.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.3.0/docs/FunctionAndClassReference.md) | 
[v0.2.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.2.0/docs/FunctionAndClassReference.md)

## Function/Class Reference

- [*function* `reset_instance()`](#function-reset_instance)
- [*function* `set_is_cli($return)`](#function-set_is_clireturn)
- [*function* `load_class_instance($classname, $instance)`](#function-load_class_instanceclassname-instance)
- [*class* TestCase](#class-testcase)
	- [`TestCase::resetInstance()`](#testcaseresetinstance)
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
- [*class* MonkeyPatch](#class-monkeypatch)
	- [`MonkeyPatch::patchFunction($function, $return_value, $class_method)`](#monkeypatchpatchfunctionfunction-return_value-class_method)
	- [`MonkeyPatch::resetFunctions()`](#monkeypatchresetfunctions)
	- [`MonkeyPatch::patchMethod($classname, $params)`](#monkeypatchpatchmethodclassname-params)
	- [`MonkeyPatch::resetMethods()`](#monkeypatchresetmethods)
	- [`MonkeyPatch::verifyInvoked($class_method, $params)`](#monkeypatchverifyinvokedclass_method-params)
	- [`MonkeyPatch::verifyInvokedOnce($class_method, $params)`](#monkeypatchverifyinvokedonceclass_method-params)
	- [`MonkeyPatch::verifyInvokedMultipleTimes($class_method, $times, $params)`](#monkeypatchverifyinvokedmultipletimesclass_method-times-params)
	- [`MonkeyPatch::verifyNeverInvoked($class_method, $params)`](#monkeypatchverifyneverinvokedclass_method-params)

### *function* `reset_instance()`

Reset CodeIgniter instance. You must create new controller instance after calling this function.

~~~php
reset_instance();
$controller = new Welcome();
$this->CI =& get_instance();
~~~

Normally, you don't have to use this function. Use [`TestCase::resetInstance()`](#testcaseresetinstance) method instead.

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

#### `TestCase::resetInstance()`

Reset CodeIgniter instance and assign new CodeIgniter instance as `$this->CI`.

~~~php
public function setUp()
{
	$this->resetInstance();
	$this->CI->load->model('Category_model');
	$this->obj = $this->CI->Category_model;
}
~~~

**Note:** When you call `$this->request()`, you don't have to use this method. Because `$this->request()` resets CodeIgniter instance internally.

**Upgrade Note for v0.6.0**

Before v0.6.0, we write `setUp()` method like this:

~~~php
public function setUp()
{
	$this->CI =& get_instance();
	$this->CI->load->model('Category_model');
	$this->obj = $this->CI->Category_model;
}
~~~

When you use the way, you use the same CodeIgniter instance and the same `Category_model` instance in every test method.

In contrast, if you use `$this->resetInstance()`, it resets CodeIgniter instance and `Category_model`. So you use new CodeIgniter instance and new `Category_model` instance in every test method.

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

### *class* MonkeyPatch

To use this class, you have to enable monkey patching. See [How to Write Tests](HowToWriteTests.md#monkey-patching).

#### `MonkeyPatch::patchFunction($function, $return_value, $class_method)`

| param         | type   | description                                    |
|---------------|--------|------------------------------------------------|
|`$function`    | string | function name to patch                         |
|`$return_value`| mixed  | return value / callback                        |
|`$class_method`| string | class::method or classname to apply this patch |

Replace function on the fly.

If `$class_method` is present, the patch is applied to the functions only in the class method or in the class.

There are some known limitations. See [How to Write Tests](HowToWriteTests.md#patching-functions) for details.

#### `MonkeyPatch::resetFunctions()`

Reset all patched functions.

This method is called on `TestCase::tearDown()` by default. So you don't have to call it normally.

#### `MonkeyPatch::patchMethod($classname, $params)`

| param       | type   | description                   |
|-------------|--------|-------------------------------|
|`$classname` | string | class name to patch           |
|`$params`    | array  | [method_name => return_value] |

Replace method in user-defined class on the fly.

#### `MonkeyPatch::resetMethods()`

Reset all patched class methods.

This method is called on `TestCase::tearDown()` by default. So you don't have to call it normally.

#### `MonkeyPatch::verifyInvoked($class_method, $params)`

| param         | type   | description              |
|---------------|--------|--------------------------|
|`$class_method`| string | class::method / function |
|`$params`      | array  | arguments                |

Verifies a patched class method or a patched function was invoked at least once.

~~~php
MonkeyPatch::verifyInvoked(
	'Ion_auth_model::login', ['foo', 'bar']
);
~~~

#### `MonkeyPatch::verifyInvokedOnce($class_method, $params)`

| param         | type   | description              |
|---------------|--------|--------------------------|
|`$class_method`| string | class::method / function |
|`$params`      | array  | arguments                |

Verifies that patched class method or a patched function was invoked only once.

~~~php
MonkeyPatch::verifyInvokedOnce(
	'CI_Input::post', ['id']
);
~~~

#### `MonkeyPatch::verifyInvokedMultipleTimes($class_method, $times, $params)`

| param         | type   | description              |
|---------------|--------|--------------------------|
|`$class_method`| string | class::method / function |
|`$times`       | int    | times                    |
|`$params`      | array  | arguments                |

Verifies that patched method or a patched function was called exactly $times times.

~~~php
MonkeyPatch::verifyInvokedMultipleTimes(
	'CI_Input::post', 2
);
~~~

#### `MonkeyPatch::verifyNeverInvoked($class_method, $params)`

| param         | type   | description              |
|---------------|--------|--------------------------|
|`$class_method`| string | class::method / function |
|`$params`      | array  | arguments                |

Verifies that patched method or a patched function was not called.

~~~php
MonkeyPatch::verifyNeverInvoked(
	'Ion_auth_model::login', ['username', 'PHS/DL1m6OMYg']
);
~~~
