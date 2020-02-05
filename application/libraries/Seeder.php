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
	/**
	 * @var CI_Controller
	 */
	private $CI;

	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * @var CI_DB_forge
	 */
	protected $dbforge;

	/**
	 * @var string
	 */
	protected $seedPath;

	/**
	 * @var array
	 */
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
	 * @param bool $callDependencies
	 */
	public function call($seeder, $callDependencies = true)
	{
		if ($this->seedPath === null)
		{
			$this->seedPath = APPPATH . 'database/seeds/';
		}

		$obj = $this->loadSeeder($seeder);
		if ($callDependencies === true && $obj instanceof Seeder) {
			$obj->callDependencies($this->seedPath);
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
	 * Call dependency seeders
	 *
	 * @param string $seedPath
	 */
	public function callDependencies($seedPath)
	{
		foreach ($this->depends as $path => $seeders) {
			$this->seedPath = $seedPath;
			if (is_string($path)) {
				$this->setPath($path);
			}

			$this->callDependency($seeders);
		}
		$this->setPath($seedPath);
	}

	/**
	 * Call dependency seeder
	 *
	 * @param string|array $seederName
	 */
	protected function callDependency($seederName)
	{
		if (is_array($seederName)) {
			array_map([$this, 'callDependency'], $seederName);
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
