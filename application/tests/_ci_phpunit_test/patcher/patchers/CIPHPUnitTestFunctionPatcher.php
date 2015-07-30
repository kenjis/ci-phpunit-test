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
	public static $replacement;

	public static function patch($source)
	{
		$patched = false;
		self::$replacement = [];

		$parser = new PhpParser\Parser(new PhpParser\Lexer(
			['usedAttributes' => array('startTokenPos', 'endTokenPos')]
		));
		$traverser = new PhpParser\NodeTraverser;
		$traverser->addVisitor(new CIPHPUnitTestFunctionPatcherNodeVisitor());

		$ast_orig = $parser->parse($source);
		$ast_new = $traverser->traverse($ast_orig);

		if (self::$replacement !== [])
		{
			$tokens = token_get_all($source);

			$patched = false;
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
}
