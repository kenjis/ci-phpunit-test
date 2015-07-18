# Requests to CodeIgniter

Writing tests for CodeIgniter 3.0 application has troublesome. Because CodeIgniter has some code which is untestable or difficult to test.

This is request list for CodeIgniter 3.x.

## Bootstrap file for PHPUnit

`core/CodeIgniter.php` calls a controller. We need bootstrap file which does not call controllers for testing.

## Functions which don't call `exit()`

* `redirect()`
* `show_error()`
* `show_404()`

`exit()` stops phpunit. We hope they throw exceptions.

## Output class has status code

If so, we can test the status code.

## Way to reset CodeIgniter object

To test singleton, we need a method to reset it.
