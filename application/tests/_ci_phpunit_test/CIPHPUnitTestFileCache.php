<?php
/**
 * Part of ci-phpunit-test
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
	private $updated = false;

	public function __construct($file)
	{
		$this->file = $file;

		if (file_exists($this->file))
		{
			$this->map = unserialize(file_get_contents($this->file));

			if (! is_array($this->map)) {
				$this->map = [];
			}

			return;
		}

		$dir = dirname($this->file);
		if (! is_dir($dir))
		{
			if (@mkdir($dir, 0777, true) === false)
			{
				throw new RuntimeException('Failed to create folder: ' . $dir);
			}
		}

		if (file_put_contents($this->file, '') === false)
		{
			throw new RuntimeException(
				'Failed to write to cache file: ' . $this->file
			);
		}
	}

	public function __destruct()
	{
		if ($this->updated) {
			file_put_contents($this->file, serialize($this->map));
		}
	}

	/**
	 * Dump cache data (sorted by key)
	 *
	 * @return array
	 */
	public function dump()
	{
		$map = $this->map;
		ksort($map);
		return $map;
	}

	#[\ReturnTypeWillChange]
	public function offsetSet($key, $value)
	{
		$this->map[$key] = $value;
		$this->updated = true;
	}

	#[\ReturnTypeWillChange]
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

	#[\ReturnTypeWillChange]
	public function offsetExists($key)
	{
		return isset($this->map[$key]);
	}

	#[\ReturnTypeWillChange]
	public function offsetUnset($key)
	{
		unset($this->map[$key]);
		$this->updated = true;
	}
}
