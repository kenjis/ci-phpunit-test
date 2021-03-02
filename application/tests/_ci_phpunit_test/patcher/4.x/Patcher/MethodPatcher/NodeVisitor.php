<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Patcher\MethodPatcher;

use Kenjis\MonkeyPatch\Patcher\MethodPatcher;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class NodeVisitor extends NodeVisitorAbstract
{
	public function leaveNode(Node $node)
	{
		if (! $node instanceof ClassMethod) {
			return;
		}

		$parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

		if ($node->returnType !== null) {
			if (isset($node->returnType->name) && $node->returnType->name === 'void') {
				$ast = $parser->parse('<?php ' . MethodPatcher::CODENORET);
			} elseif (isset($node->returnType->parts) && $node->returnType->parts[0] === 'void') {
				$ast = $parser->parse('<?php ' . MethodPatcher::CODENORET);
			} else {
				$ast = $parser->parse('<?php ' . MethodPatcher::CODE);
			}
		} else {
			$ast = $parser->parse('<?php ' . MethodPatcher::CODE);
		}

		if ($node->stmts !== null) {
			array_unshift(
				$node->stmts,
				$ast[0]
			);
		}
	}
}
