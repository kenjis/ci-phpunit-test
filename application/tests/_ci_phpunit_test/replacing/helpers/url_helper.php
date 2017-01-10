<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

/**
 * CodeIgniter URL Helpers with little modification for testing
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Kenji Suzuki <http://github.com/kenjis/ci-phpunit-test>
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/helpers/url_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('redirect'))
{
	function redirect($uri = '', $method = 'auto', $code = NULL)
	{
		if ( ! preg_match('#^(\w+:)?//#i', $uri))
		{
			$uri = site_url($uri);
		}

		// IIS environment likely? Use 'refresh' for better compatibility
		if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE)
		{
			$method = 'refresh';
		}
		elseif ($method !== 'refresh' && (empty($code) OR ! is_numeric($code)))
		{
			if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1')
			{
				$code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
					? 303	// reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
					: 307;
			}
			else
			{
				$code = 302;
			}
		}

		switch ($method)
		{
			case 'refresh':
				if (ENVIRONMENT !== 'testing')
				{
					header('Refresh:0;url='.$uri);
				}
				break;
			default:
				if (ENVIRONMENT !== 'testing')
				{
					header('Location: '.$uri, TRUE, $code);
				}
				break;
		}

		if (ENVIRONMENT !== 'testing')
		{
			exit;
		}
		else
		{
			while (ob_get_level() > 1)
			{
				ob_end_clean();
			}

			throw new CIPHPUnitTestRedirectException('Redirect to ' . $uri, $code);
		}
	}
}
