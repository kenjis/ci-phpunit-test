<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Patcher;

require __DIR__ . '/../vendor/PHP-Parser/lib/bootstrap.php';
require __DIR__ . '/FunctionPatcher/NodeVisitor.php';
require __DIR__ . '/FunctionPatcher/Proxy.php';

use LogicException;

use PhpParser\Parser;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;

use Kenjis\MonkeyPatch\Patcher\FunctionPatcher\NodeVisitor;

class FunctionPatcher
{
	/**
	 * @var array list of function names (in lower case) which you don't patch
	 */
	private static $blacklist = [
		// Segmentation fault
		'call_user_func_array',
		'exit__',
		// Error: Only variables should be assigned by reference
		'get_instance',
		'get_config',
		'load_class',
		'get_mimes',
		'_get_validation_object',
		// has reference param
		'preg_replace',
		'preg_match',
		'preg_match_all',
		'array_unshift',
		'array_shift',
		'sscanf',
		'ksort',
		'krsort',
		// Special functions for ci-phpunit-test
		'show_404',
		'show_error',
		'redirect'
	];

	public static $replacement;

	public static function addBlacklist($function_name)
	{
		self::$blacklist[] = strtolower($function_name);
	}

	public static function removeBlacklist($function_name)
	{
		$key = array_search(strtolower($function_name), self::$blacklist);
		array_splice(self::$blacklist, $key, 1);
	}

	/**
	 * @param string $name function name
	 * @return boolean
	 */
	public static function isBlacklisted($name)
	{
		if (in_array(strtolower($name), self::$blacklist))
		{
			return true;
		}

		return false;
	}

	public static function patch($source)
	{
		$patched = false;
		self::$replacement = [];

		$parser = new Parser(new Lexer(
			['usedAttributes' => ['startTokenPos', 'endTokenPos']]
		));
		$traverser = new NodeTraverser;
		$traverser->addVisitor(new NodeVisitor());

		$ast_orig = $parser->parse($source);
		$traverser->traverse($ast_orig);

		if (self::$replacement !== [])
		{
			$patched = true;
			$new_source = self::generateNewSource($source);
		}
		else
		{
			$new_source = $source;
		}

		return [
			$new_source,
			$patched,
		];
	}

	protected static function generateNewSource($source)
	{
		$tokens = token_get_all($source);
		$new_source = '';
		$i = -1;

		ksort(self::$replacement);
		reset(self::$replacement);
		$replacement = each(self::$replacement);

		foreach ($tokens as $token)
		{
			$i++;

			if (is_string($token))
			{
				$new_source .= $token;
			}
			elseif ($i == $replacement['key'])
			{
				$new_source .= $replacement['value'];
				$replacement = each(self::$replacement);
			}
			else
			{
				$new_source .= $token[1];
			}
		}

		if ($replacement !== false)
		{
			throw new LogicException('Replacement data still remain');
		}

		return $new_source;
	}
}
