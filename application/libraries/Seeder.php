<?php
/**
 * Part of CI PHPUnit Test
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
	private static $call_migration = false;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->dbforge();
		$this->db      = $this->CI->db;
		$this->load    = $this->CI->load;
		$this->dbforge = $this->CI->dbforge;

		if (!defined('DB_SECURITY') || DB_SECURITY) {
			// security measure 2: only load if used database ends on '_test'
			$len = strlen($this->db->database);

			if (substr($this->db->database, $len - 5, $len) != '_test') {
				die(
					PHP_EOL . 'Sorry, the name of your test database must end on \'_test\'.' .
					PHP_EOL . 'This prevents deleting important data by accident.' . PHP_EOL
				);
			}
		}

		if (!self::$call_migration) {
			$this->callMigration();
			self::$call_migration = true;
		}
	}

	/**
	 * Run another seeder
	 * 
	 * @param string $seeder Seeder classname
	 */
	public function call($seeder)
	{
		$base_seeds = APPPATH . 'database/seeds/';
		$file = $base_seeds . $seeder . '.php';
		if (!file_exists($file)) {
			$seeder .= 'Seeder';
			$file = $base_seeds . $seeder . 'Seeder.php';
		}
		if (file_exists($file)) {
			require_once $file;
			$obj = new $seeder;
			$obj->run();
		}
	}

	/**
	 * Truncate current table
	 */
	public function unload($table)
	{
		if (isset($this->table) && !empty($this->table)) {
			$this->db->truncate($this->table);
		}
	}

	/**
	 * Enable the use of CI super-global
	 *
	 * @param   mixed   $var
	 * @return  mixed
	 */
	public function __get($property)
	{
		return $this->CI->$property;
	}

	/**
	 * Run migrations
	 */
	public function callMigration() {
		$this->config->load('migration');
		if ($this->config->item('migration_enabled') === false) {
			echo PHP_EOL . "\033[43m\033[1;37mDisabled migrations.\033[0m" .  PHP_EOL;
			return;
		}

		foreach ($this->db->list_tables() as $table) {
			$this->dbforge->drop_table($table, true);
		}

		isset($this->migration) OR $this->load->library('migration');

		if ($this->config->item('migration_enabled') === true && $this->migration->current() === false) {
			throw new Exception($this->migration->error_string());
		}
	}
}
