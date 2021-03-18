<?php

class CIPHPUnitTestDbConnectionStore
{
	private static $connections = [];

	public static function add(CI_DB $db)
	{
		self::$connections[] = $db;
	}

	public static function destory()
	{
		foreach (self::$connections as $db) {
			self::closeConnection($db);
		}

		self::$connections = [];
	}

	public static function closeConnection(CI_DB $db)
	{
		if ($db->dsn === 'sqlite::memory:' && $db->database === ':memory:') {
			return;
		}

		self::cleanUpReference($db);
		$db->close();
	}

	private static function cleanUpReference(CI_DB $db)
	{
		if ($db->dbdriver === 'oci8') {
			if (is_resource($db->curs_id)) {
				oci_free_statement($db->curs_id);
			}
			if (is_resource($db->stmt_id)) {
				oci_free_statement($db->stmt_id);
			}
		} elseif ($db->dbdriver === 'pdo') {
			$db->result_id = null;
		}
	}
}
