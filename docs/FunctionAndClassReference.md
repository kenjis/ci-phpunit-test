# CI PHPUnit Test for CodeIgniter 3.0

## Function/Class Reference

### *function* reset_instance()

Reset CodeIgniter instance.

### [Deprecated] *function* get_new_instance()

`returns` CI_Controller instance

Generate new CodeIgniter instance and get it.

### *function* set_is_cli($return)

`$return`: (bool) return value to set

Set return value of `is_cli()` function.

~~~php
set_is_cli(FALSE);
~~~

### *function* load_class_instance($classname, $instance)

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

#### TestCase::request($method, $argv, $params = [], $callable = null)

| param     | type         | description                                    |
|-----------|--------------|------------------------------------------------|
|`$method`  | string       | HTTP method                                    |
|`$argv`    | array|string | controller, method [, arg1, ...] | URI string  |
|`$params`  | array        | POST parameters / Query string                 |
|`$callable`| callable     | function to run after controller instantiation |

`returns` (string) output strings (view)

Run a controller method after `reset_instance()`.

~~~php
$output = $this->request('GET', ['Form', 'index']);
~~~

~~~php
$load_agent = function ($CI) {
	$CI->load->library('user_agent');
};
$output = $this->request('GET', ['Bbs', 'index'], [], $load_agent);
~~~

#### TestCase::getDouble($classname, $params)

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

#### TestCase::verifyInvoked($mock, $method, $params)

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

#### TestCase::verifyInvokedOnce($mock, $method, $params)

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

#### TestCase::verifyInvokedMultipleTimes($mock, $method, $times, $params)

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

#### TestCase::verifyNeverInvoked($mock, $method, $params)

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

#### TestCase::warningOff()

Turn off WARNING in error reporting.

#### TestCase::warningOn()

Restore error reporting.
