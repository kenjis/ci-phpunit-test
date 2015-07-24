<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestExitException extends RuntimeException
{
	public $file;
	public $line;
	public $class;
	public $method;
	public $exit_status;
}

function exit_($status = null)
{
	$trace = debug_backtrace();
	$file = $trace[0]['file'];
	$line = $trace[0]['line'];
	$class = isset($trace[1]['class']) ? $trace[1]['class'] : null;
	$method = $trace[1]['function'];

	if ($class === null)
	{
		$message = 'exit() called in ' . $method . '() function';
	}
	else
	{
		$message = 'exit() called in ' . $class . '::' . $method . '()';
	}
	

	$exception = new CIPHPUnitTestExitException($message);
	$exception->file = $file;
	$exception->line = $line;
	$exception->class = $class;
	$exception->method = $method;
	$exception->exit_status = $status;

	throw $exception;
}
