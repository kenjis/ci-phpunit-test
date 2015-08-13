# Change Log for CI PHPUnit Test

## v0.6.2 (2015/08/13)

### Fixed

* Fix bug that can't test model classes (classes in `application/models` folder) which do not extend `CI_Model` more than once.

## v0.6.1 (2015/08/12)

### Changed

* How to enable Monkey Patching has been changed. `TestCase::$enable_patcher` was removed. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.6.1/docs/HowToWriteTests.md#monkey-patching).

### Added

* Monkey Patching on functions. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.6.1/docs/HowToWriteTests.md#patching-functions).
* Monkey Patching on methods in user-defined classes. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.6.1/docs/HowToWriteTests.md#patching-methods-in-user-defined-classes).
* `$this->resetInstance()` for better model testing. See [#40](https://github.com/kenjis/ci-phpunit-test/pull/40).

### Removed

* `TestCase::$enable_patcher` (Introduced in v0.5.0)

### Others

* Compatible with CodeIgniter 3.0.1

## v0.5.0 (2015/07/27)

### Changed

* Now *CI PHPUnit Test* replaces `redirect()` function by default. See [#33](https://github.com/kenjis/ci-phpunit-test/pull/33).

### Added

* Monkey Patching on `exit()`. *CI PHPUnit Test* could convert `exit()` in your code to Exception on the fly. See [#32](https://github.com/kenjis/ci-phpunit-test/pull/32).
* `$this->request->setCallablePreConstructor()` to inject mocks into your controller constructors. See [#29](https://github.com/kenjis/ci-phpunit-test/pull/29).

### Fixed

* Fix bug that PHPUnit debug info of the first test is not outputted.

### Removed

* `get_new_instance()` (deprecated since pre v0.1.0)

## v0.4.0 (2015/07/21)

### Changed

* Changed `MY_url_helper.php` as sample. If you use new `MY_url_helper.php`, you must update your tests for `redirect()` using new `$this->assertRedirect()` method. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/HowToWriteTests.md#redirect) and [#28](https://github.com/kenjis/ci-phpunit-test/pull/28).
* Changed how to test `show_404()` and `show_error()`. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/HowToWriteTests.md#show_error-and-show_404) and [#28](https://github.com/kenjis/ci-phpunit-test/pull/28).

### Added

* `$this->assertResponseCode()` to check response code in controller tests. See [Function/Class Reference](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/FunctionAndClassReference.md#testcaseassertresponsecodecode).
* `$this->assertRedirect()` to check if `redirect()` is called in controller tests. See [Function/Class Reference](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/FunctionAndClassReference.md#testcaseassertredirecturi-code--null).
* Property `$bc_mode_throw_PHPUnit_Framework_Exception` in `CIPHPUnitTestRequest` class

### Deprecated

* Property `$bc_mode_throw_PHPUnit_Framework_Exception` in `CIPHPUnitTestRequest` class

### Others

* Improved documentation. See [How to Write Test](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/HowToWriteTests.md).

## v0.3.0 (2015/07/14)

### Deprecated

* 4th param `$callable` of `$this->request()` and  `$this->ajaxRequest()`. Use `$this->request->setCallable()` method instead. See [Function/Class Reference](https://github.com/kenjis/ci-phpunit-test/blob/v0.3.0/docs/FunctionAndClassReference.md#testcaserequestmethod-argv-params---callable--null).

### Added

* `$this->request->setCallable()` See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.3.0/docs/HowToWriteTests.md#request-and-use-mocks).
* You can enable hooks for controller in controller testing. `$this->request->enableHooks()` is added. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.3.0/docs/HowToWriteTests.md#controller-with-hooks).

## v0.2.0 (2015/06/19)

* Change `MY_url_helper.php` as sample. If you use new `MY_url_helper.php`, you must catch `PHPUnit_Framework_Exception` when you test code using `redirect()`.
* Improve documentation

## v0.1.1 (2015/06/15)

* Improve installation. See [Installation](https://github.com/kenjis/ci-phpunit-test#installation).
* Fix bug that Bootstrap outputs 404 page when 404_override
* Fix bug that risky tests occur [#14](https://github.com/kenjis/ci-phpunit-test/issues/14)

## v0.1.0 (2015/06/12)

* Initial version
* Compatible with CodeIgniter 3.0.0
