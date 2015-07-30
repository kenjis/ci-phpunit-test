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
	public static function patch($source)
	{
		$patched = false;

		$parser = new PhpParser\Parser(new PhpParser\Lexer);
		$traverser = new PhpParser\NodeTraverser;
		$traverser->addVisitor(new CIPHPUnitTestFunctionPatcherNodeVisitor());

		$ast_orig = $parser->parse($source);
		$ast_new = $traverser->traverse($ast_orig);

		if ($ast_orig != $ast_new)
		{
			$patched = true;
			$prettyPrinter = new PhpParser\PrettyPrinter\Standard();
			$new_source = $prettyPrinter->prettyPrintFile($ast_new);
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
