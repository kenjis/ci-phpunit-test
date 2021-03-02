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

require __DIR__ . '/MethodPatcher/NodeVisitor.php';
require __DIR__ . '/MethodPatcher/PatchManager.php';

use Kenjis\MonkeyPatch\MonkeyPatchManager;
use Kenjis\MonkeyPatch\Patcher\MethodPatcher\NodeVisitor;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;

class MethodPatcher extends AbstractPatcher
{
	const CODE = <<<'EOL'
if (($__ret__ = \__PatchManager__::getReturn(__CLASS__, __FUNCTION__, func_get_args())) !== __GO_TO_ORIG__) return $__ret__;
EOL;
	const CODENORET = <<<'EOL'
if (($__ret__ = \__PatchManager__::getReturn(__CLASS__, __FUNCTION__, func_get_args())) !== __GO_TO_ORIG__) return;
EOL;

	public static $replacement;

	public function __construct()
	{
		$this->node_visitor = new NodeVisitor();
	}

	public function patch($source)
	{
		$patched = false;

		$parser = (new ParserFactory())
			->create(
				MonkeyPatchManager::getPhpParser(),
				new Lexer(
					['usedAttributes' => ['startTokenPos', 'endTokenPos']]
				)
			);
		$traverser = new NodeTraverser();
		$traverser->addVisitor($this->node_visitor);

		$ast_orig = $parser->parse($source);
		$prettyPrinter = new PrettyPrinter\Standard();
		$source_ = $prettyPrinter->prettyPrintFile($ast_orig);

		$ast = $parser->parse($source);
		$traverser->traverse($ast);

		$new_source = $prettyPrinter->prettyPrintFile($ast);

		if ($source_ !== $new_source) {
			$patched = true;
		}

		return [
			$new_source,
			$patched,
		];
	}
}
