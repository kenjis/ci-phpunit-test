<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestSuperGlobal
{
	public static function set_Global($name, $value)
	{
		$GLOBALS[$name] = $value;
	}

	public function set_POST($params)
	{
		if (is_array($params))
		{
			if ($_SERVER['REQUEST_METHOD'] === 'POST')
			{
				$_POST = $params;
			}
		}
	}

	public function set_FILES(array $files)
	{
		if (count($files) > 1)
		{
			throw new LogicException('Invalid $_FILES: '.var_export($files, true));
		}

		$field = array_keys($files)[0];
		$is_multiple = is_array($files[$field]['tmp_name']);

		if ($is_multiple)
		{
			throw new LogicException('Cannot handle multiple file upload. $_FILES: '.var_export($files, true));
		}

		// Handle single file
		$file =& $files[$field];

		if (! file_exists($file['tmp_name']))
		{
			$file['error'] = UPLOAD_ERR_NO_FILE;
			$file['type'] = '';
			$file['size'] = 0;

			$_FILES = $files;
			return;
		}

		$file['size'] = filesize($file['tmp_name']);

		$upload_max_filesize = $this->convertToBytes(ini_get('upload_max_filesize'));
		if ($upload_max_filesize < $file['size'])
		{
			$file['error'] = UPLOAD_ERR_INI_SIZE;

			$_FILES = $files;
			return;
		}

		$file['error'] = UPLOAD_ERR_OK;

		$_FILES = $files;
		return;
	}

	private function convertToBytes($value)
	{
		if (is_numeric($value))
		{
			return $value;
		}
		else
		{
			$value_length = strlen($value);
			$number = substr($value, 0, $value_length - 1);
			$unit = strtolower(substr($value, $value_length - 1));

			switch ($unit)
			{
				case 'k':
					$number *= 1024;
					break;
				case 'm':
					$number *= 1024 * 1024;
					break;
				case 'g':
					$number *= 1024 * 1024 * 1024;
					break;
			}

			return $number;
		}
	}

	public function set_GET(&$argv, $params)
	{
		if (is_string($argv))
		{
			$query_string = $this->getQueryString($argv);
			if ($query_string !== null)
			{
				// Set $_GET if URI string has query string
				parse_str($query_string, $_GET);
				// Remove query string from URI string
				$argv = substr($argv, 0, -strlen($query_string)-1);
			}
		}

		if (is_array($params))
		{
			if ($_SERVER['REQUEST_METHOD'] === 'GET')
			{
				// if GET params are passed, overwrite $_GET
				if ($params !== [])
				{
					$_GET = $params;
				}
			}
		}
	}

	public function set_SERVER_REQUEST_URI($argv)
	{
		$path = '';
		if (is_string($argv))
		{
			$path = $argv;
		}
		elseif (is_array($argv))
		{
			// Generate URI path from array of controller, method, arg, ...
			$path = implode('/', $argv);
		}

		if ($_GET !== [])
		{
			$_SERVER['REQUEST_URI'] =
				'/' . $path . '?'
				. http_build_query($_GET);
		}
		else
		{
			$_SERVER['REQUEST_URI'] = '/' . $path;
		}
	}

	/**
	 * Parse URI string and Get query string
	 * 
	 * @param string $uri
	 * @return string|null
	 * @throws LogicException
	 */
	protected function getQueryString($uri)
	{
		$query_string = parse_url('http://localhost/'.$uri, PHP_URL_QUERY);

		if ($query_string === false)
		{
			throw new LogicException('Bad URI string: ' . $uri);
		}

		return $query_string;
	}

	/**
	 * Set HTTP request header to $_SERVER
	 * 
	 * @param string $name  header name
	 * @param string $value value
	 */
	public function set_SERVER_HttpHeader($name, $value)
	{
		$normalized_name = str_replace('-', '_', strtoupper($name));

		if (
			$normalized_name === 'CONTENT_LENGTH' 
			|| $normalized_name === 'CONTENT_TYPE'
		)
		{
			$key = $normalized_name;
		}
		else
		{
			$key = 'HTTP_' . $normalized_name;
		}

		$_SERVER[$key] = $value;
	}
}
