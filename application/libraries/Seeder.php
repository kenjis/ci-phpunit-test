<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Seeder
{
	private $CI;
	protected $db;
	protected $dbforge;
	protected $seedPath;
	protected $depends = [];

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->dbforge();
		$this->db = $this->CI->db;
		$this->dbforge = $this->CI->dbforge;
	}

	/**
	 * Run another seeder
	 *
	 * @param string $seeder Seeder classname
	 */
	public function call($seeder, $call_depends = true)
	{
		if ($this->seedPath === null)
		{
			$this->seedPath = APPPATH . 'database/seeds/';
		}

		$obj = $this->loadSeeder($seeder);
		if ($call_depends === true && $obj instanceof Seeder) {
			$obj->callDepends($this->seedPath);
		}
		$obj->run();
	}

	/**
	 * Get Seeder instance
	 *
	 * @param string $seeder
	 * @return Seeder
	 */
	protected function loadSeeder($seeder)
	{
		$file = $this->seedPath . $seeder . '.php';
		require_once $file;

		return new $seeder;
	}

	/**
	 * Call depend seeder list
	 *
	 * @param string $seedPath
	 */
	public function callDepends($seedPath)
	{
		foreach ($this->depends as $path => $seeders) {
			$this->seedPath = $seedPath;
			if (is_string($path)) {
				$this->setPath($path);
			}

			$this->callDepend($seeders);
		}
		$this->setPath($seedPath);
	}

	/**
	 * Call depend seeder
	 *
	 * @param string|array $seederName
	 */
	protected function callDepend($seederName)
	{
		if (is_array($seederName)) {
			array_map([$this, 'callDepend'], $seederName);
			return;
		}

		$seeder = $this->loadSeeder($seederName);
		if (is_string($this->seedPath)) {
			$seeder->setPath($this->seedPath);
		}

		$seeder->call($seederName, true);
	}

	/**
	 * Set path for seeder files
	 *
	 * @param string $path
	 */
	public function setPath($path)
	{
		$this->seedPath = rtrim($path, '/').'/';
	}

	public function __get($property)
	{
		return $this->CI->$property;
	}
}
