<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

/**
 * Copyright for Original Code
 * 
 * @author     Adrian Philipp
 * @copyright  2014 Adrian Philipp
 * @license    https://github.com/adri/monkey/blob/dfbb93ae09a2c0712f43eab7ced76d3f49989fbe/LICENSE
 * @link       https://github.com/adri/monkey
 * 
 * @see        https://github.com/adri/monkey/blob/dfbb93ae09a2c0712f43eab7ced76d3f49989fbe/testTest.php
 */

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;

class CIPHPUnitTestFunctionPatcherNodeVisitor extends PhpParser\NodeVisitorAbstract
{
	public function leaveNode(PhpParser\Node $node)
	{
		if (! ($node instanceof FuncCall))
		{
			return;
		}

		if (! ($node->name instanceof Name))
		{
			return;
		}

		if (
			$node->name->isUnqualified()
			&& ! CIPHPUnitTestFunctionPatcher::isBlacklisted((string) $node->name)
		) {
			$replacement = new FullyQualified(array());
			$replacement->set(
				'CIPHPUnitTestFunctionPatcherProxy::' . (string) $node->name
			);

			$pos = $node->getAttribute('startTokenPos');
			CIPHPUnitTestFunctionPatcher::$replacement[$pos] = 
				'\CIPHPUnitTestFunctionPatcherProxy::' . (string) $node->name;

			$node->name = $replacement;
		}
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	protected function isInternalFunction($name)
	{
		try {
			$ref_func = new ReflectionFunction($name);
			return $ref_func->isInternal();
		} catch (ReflectionException $e) {
			// ReflectionException: Function xxx() does not exist
			return false;
		}
	}
}
