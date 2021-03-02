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

use Kenjis\MonkeyPatch\Patcher\MethodPatcher\NodeVisitor;

class MethodPatcher extends AbstractPatcher
{
	const CODE = <<<'EOL'
if (($__ret__ = \__PatchManager__::getReturn(__CLASS__, __FUNCTION__, func_get_args())) !== __GO_TO_ORIG__) return $__ret__;
EOL;
	const CODENORET = <<<'EOL'
if (($__ret__ = \__PatchManager__::getReturn(__CLASS__, __FUNCTION__, func_get_args())) !== __GO_TO_ORIG__) return;
EOL;

	public function __construct()
	{
		$this->node_visitor = new NodeVisitor();
	}
}
