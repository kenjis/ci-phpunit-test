<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

use PhpParser\Node\Stmt\ClassMethod;

class CIPHPUnitTestMethodPatcherNodeVisitor extends PhpParser\NodeVisitorAbstract
{
	public function leaveNode(PhpParser\Node $node)
	{
		if (! ($node instanceof ClassMethod))
		{
			return;
		}

		$pos = $node->getAttribute('startTokenPos');
		CIPHPUnitTestMethodPatcher::$replacement[$pos] = true;
	}
}
