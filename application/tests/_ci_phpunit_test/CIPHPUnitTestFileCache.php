<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestFileCache implements ArrayAccess
{
	private $file;
	private $map = [];

	public function __construct($file)
	{
		$this->file = $file;
		
		if (file_exists($file))
		{
			$this->map = unserialize(file_get_contents($file));
		}
		
		if (! is_dir(dirname($file)))
		{
			if (! @mkdir($dir, 0777, true))
			{
				throw new RuntimeException('Failed to create folder: ' . $dir);
			}
		}
	}

	public function __destruct()
	{
		file_put_contents($this->file, serialize($this->map));
	}

	public function offsetSet($key, $value)
	{
		$this->map[$key] = $value;
	}

	public function offsetGet($key)
	{
		if ($this->offsetExists($key))
		{
			return $this->map[$key];
		}
		else
		{
			return null;
		}
	}

	public function offsetExists($key)
	{
		return isset($this->map[$key]);
	}

	public function offsetUnset($key)
	{
		unset($this->map[$key]);
	}
}
