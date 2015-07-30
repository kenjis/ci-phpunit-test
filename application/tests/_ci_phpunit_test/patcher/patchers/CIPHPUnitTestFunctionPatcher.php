<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

require __DIR__ . '/../vendor/PHP-Parser/lib/bootstrap.php';
require __DIR__ . '/CIPHPUnitTestFunctionPatcher/CIPHPUnitTestFunctionPatcherNodeVisitor.php';
require __DIR__ . '/CIPHPUnitTestFunctionPatcher/CIPHPUnitTestFunctionPatcherProxy.php';

class CIPHPUnitTestFunctionPatcher
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

		$parser = new PhpParser\Parser(new PhpParser\Lexer(
			['usedAttributes' => ['startTokenPos', 'endTokenPos']]
		));
		$traverser = new PhpParser\NodeTraverser;
		$traverser->addVisitor(new CIPHPUnitTestFunctionPatcherNodeVisitor());

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
