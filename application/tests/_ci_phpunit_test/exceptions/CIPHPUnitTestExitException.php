<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestExitException extends RuntimeException
{
	public $class;
	public $method;
	public $exit_status;

	public function setFile($file)
	{
		$this->file = $file;
	}

	public function setLine($line)
	{
		$this->line = $line;
	}
}
