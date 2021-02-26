<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Patcher;

use PhpParser\ParserFactory;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter;
use Kenjis\MonkeyPatch\MonkeyPatchManager;

abstract class AbstractPatcher
{
	protected $node_visitor;

	public static $replacement;

	public function patch($source)
	{
		$patched = false;
		static::$replacement = [];

		$parser = (new ParserFactory)
			->create(
				MonkeyPatchManager::getPhpParser(),
				new Lexer(
					['usedAttributes' => ['startTokenPos', 'endTokenPos']]
				)
			);
		$traverser = new NodeTraverser;
		$traverser->addVisitor($this->node_visitor);

		$ast_orig = $parser->parse($source);
		$prettyPrinter = new PrettyPrinter\Standard();
		$source_ = $prettyPrinter->prettyPrintFile($ast_orig);

		$ast = $parser->parse($source);
		$traverser->traverse($ast);

		if (static::$replacement !== [])
		{
			$patched = true;
			$new_source = static::generateNewSource($source_);
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
