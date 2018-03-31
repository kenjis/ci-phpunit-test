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
	public function call($seeder)
	{
		if ($this->seedPath === null)
		{
			$this->seedPath = APPPATH . 'database/seeds/';
		}

		$obj = $this->loadSeeder($seeder);
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
