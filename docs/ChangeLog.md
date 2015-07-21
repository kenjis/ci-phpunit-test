# Change Log for CI PHPUnit Test

## v0.4.0 (2015/07/21)

### Changed

* Changed `MY_url_helper.php` as sample. If you use new `MY_url_helper.php`, you must update your tests for `redirect()` using new `$this->assertRedirect()` method. See [How to Write Tests](HowToWriteTests.md#redirect) and [#28](https://github.com/kenjis/ci-phpunit-test/pull/28).
* Changed how to test `show_404()` and `show_error()`. See [How to Write Tests](HowToWriteTests.md#show_error-and-show_404) and [#28](https://github.com/kenjis/ci-phpunit-test/pull/28).

### Added

* `$this->assertResponseCode()` to check response code in controller tests. See [Function/Class Reference](FunctionAndClassReference.md#testcaseassertresponsecodecode).
* `$this->assertRedirect()` to check if `redirect()` is called in controller tests. See [Function/Class Reference](FunctionAndClassReference.md#testcaseassertredirecturi-code--null).
* Property `$bc_mode_throw_PHPUnit_Framework_Exception` in `CIPHPUnitTestRequest` class

### Deprecated

* Property `$bc_mode_throw_PHPUnit_Framework_Exception` in `CIPHPUnitTestRequest` class

### Others

* Improved documentation. See [How to Write Test](HowToWriteTests.md).

## v0.3.0 (2015/07/14)

### Deprecated

* 4th param `$callable` of `$this->request()` and  `$this->ajaxRequest()`. Use `$this->request->setCallable()` method instead. See [Function/Class Reference](https://github.com/kenjis/ci-phpunit-test/blob/7ef8acd7d7f80c1cf342a12f9464d2156b749b92/docs/FunctionAndClassReference.md#testcaserequestmethod-argv-params---callable--null).

### Added

* `$this->request->setCallable()` See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/7ef8acd7d7f80c1cf342a12f9464d2156b749b92/docs/HowToWriteTests.md#request-and-use-mocks).
* You can enable hooks for controller in controller testing. `$this->request->enableHooks()` is added. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/7ef8acd7d7f80c1cf342a12f9464d2156b749b92/docs/HowToWriteTests.md#controller-with-hooks).

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
