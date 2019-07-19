<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2016 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

/**
 * Copyright for Original Code
 * 
 * @author     CodeIgniter Dev Team
 * @copyright  Copyright (c) 2014 - 2016, British Columbia Institute of Technology (http://bcit.ca/)
 * @license    http://opensource.org/licenses/MIT	MIT License
 * @link       http://codeigniter.com
 * 
 * @see        https://github.com/bcit-ci/CodeIgniter4/blob/59e1587a9875141586f8333ff9cc64cdae2173c4/system/Test/CIDatabaseTestCase.php
 */

class CIPHPUnitTestDbTestCase extends CIPHPUnitTestCase
{
	protected $db;

	/**
	 * Stores information needed to remove any
	 * rows inserted via $this->hasInDatabase();
	 *
	 * @var array
	 */
	protected $insertCache = [];

	protected function loadDependencies()
	{
		if ($this->db === null)
		{
			$CI =& get_instance();
			$CI->load->database();
			$this->db = $CI->db;
		}
	}

	protected function setUp(): void
	{
		$this->loadDependencies();
	}

	//--------------------------------------------------------------------

	/**
	 * Takes care of any required cleanup after the test, like
	 * removing any rows inserted via $this->hasInDatabase()
	 */
	protected function tearDown(): void
	{
		if (! empty($this->insertCache))
		{
			foreach ($this->insertCache as $row)
			{
				$this->db->delete($row[0], $row[1]);
			}
		}
	}

	/**
	 * Workaround for the following error
	 *
	 *   Error: Call to a member function quote() on boolean
	 *   vendor/codeigniter/framework/system/database/drivers/pdo/pdo_driver.php:234
	 *
	 * I don't know why, but when I call $this->seeInDatabase() after $this->request(),
	 * I got it
	 */
	private function checkDbConnId()
	{
		if (is_object($this->db->conn_id) || is_resource($this->db->conn_id)) {
			return;
		}

		$this->db->close();
		$this->db = null;

		$CI =& get_instance();
		$CI->load->database();
		$this->db = $this->CI->db;
	}

	//--------------------------------------------------------------------
	// Database Test Helpers
	//--------------------------------------------------------------------

	/**
	 * Asserts that records that match the conditions in $where do
	 * not exist in the database.
	 *
	 * @param string $table
	 * @param array  $where
	 *
	 * @return bool
	 */
	public function dontSeeInDatabase($table, array $where)
	{
		$this->checkDbConnId();

		$this->db->from($table);
		$this->db->where($where);
		$count = $this->db->count_all_results();

		$this->assertTrue($count == 0, 'Row was found in database');
	}
	
	//--------------------------------------------------------------------

	/**
	 * Asserts that records that match the conditions in $where DO
	 * exist in the database.
	 * 
	 * @param string $table
	 * @param array  $where
	 *
	 * @return bool
	 */
	public function seeInDatabase($table, array $where)
	{
		$this->checkDbConnId();

		$this->db->from($table);
		$this->db->where($where);
		$count = $this->db->count_all_results();

		$this->assertTrue($count > 0, 'Row not found in database');
	}

	//--------------------------------------------------------------------

	/**
	 * Fetches a single column from a database row with criteria
	 * matching $where.
	 *
	 * @param string $table
	 * @param string $column
	 * @param array  $where
	 *
	 * @return bool
	 */
	public function grabFromDatabase($table, $column, array $where)
	{
		$this->checkDbConnId();

		$this->db->select($column);
		$this->db->where($where);
		$query = $this->db->get($table);
		$row = $query->row();

		return isset($row->$column) ? $row->$column : false;
	}
	
	//--------------------------------------------------------------------

	/**
	 * Inserts a row into to the database. This row will be removed
	 * after the test has run.
	 *
	 * @param string $table
	 * @param array  $data
	 *
	 */
	public function hasInDatabase($table, array $data)
	{
		$this->checkDbConnId();

		$this->insertCache[] = [
			$table, $data
		];

		$this->db->insert($table, $data);
	}

	//--------------------------------------------------------------------

	/**
	 * Asserts that the number of rows in the database that match $where
	 * is equal to $expected.
	 *
	 * @param int    $expected
	 * @param string $table
	 * @param array  $where
	 *
	 * @return bool
	 */
	public function seeNumRecords($expected, $table, array $where = [])
	{
		$this->checkDbConnId();

		$this->db->from($table);
		$this->db->where($where);
		$count = $this->db->count_all_results();

		$this->assertEquals($expected, $count, 'Wrong number of matching rows in database.');
	}
	
	//--------------------------------------------------------------------
	
}
