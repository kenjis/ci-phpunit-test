<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2020 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestLogger
{
	/**
	 * @var array
	 */
	private static $logs = [];

	private function __construct()
	{
	}

//	/**
//	 * @return array
//	 */
//	public static function getLogs()
//	{
//		return self::$logs;
//	}

	public static function resetLogs()
	{
		self::$logs = [];
	}

	/**
	 * @param string $level
	 * @param string $message
	 */
	public static function log($level, $message)
	{
		$trace = debug_backtrace();
		$file  = null;

		foreach ($trace as $row)
		{
			if (in_array($row['function'], ['log_message']))
			{
				$file = self::cleanFileNames($row['file'] ? $row['file'] : '');
				break;
			}
		}

		self::$logs[] = [
			'level' => $level,
			'message' => $message,
			'file' => $file,
		];
	}

	/**
	 * @param string $level
	 * @param string $message
	 *
	 * @return bool
	 */
	public static function didLog($level, $message)
	{
		foreach (self::$logs as $log) {
			if (strtolower($log['level']) === strtolower($level) && $message === $log['message']) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $file
	 *
	 * @return string
	 */
	private static function cleanFileNames($file)
	{
		$file = str_replace(APPPATH, 'APPPATH/', $file);
		$file = str_replace(BASEPATH, 'BASEPATH/', $file);

		return str_replace(FCPATH, 'FCPATH/', $file);
	}
}
