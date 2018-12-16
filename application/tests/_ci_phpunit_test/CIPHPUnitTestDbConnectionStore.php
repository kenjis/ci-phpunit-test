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

	private static function closeConnection(CI_DB $db)
	{
		if ($db->dsn !== 'sqlite::memory:' && $db->database !== ':memory:') {
			$db->close();
		}
	}
}
