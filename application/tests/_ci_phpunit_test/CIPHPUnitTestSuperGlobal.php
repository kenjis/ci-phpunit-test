<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestSuperGlobal
{
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
		if ($_GET !== [] && is_string($argv))
		{
			$_SERVER['REQUEST_URI'] = 
				'/' . $argv . '?'
				. http_build_query($_GET);
		}
		elseif (is_string($argv))
		{
			$_SERVER['REQUEST_URI'] = '/' . $argv;
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
