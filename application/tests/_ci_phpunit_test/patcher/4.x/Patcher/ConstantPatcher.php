<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2016 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

namespace Kenjis\MonkeyPatch\Patcher;

require __DIR__ . '/ConstantPatcher/NodeVisitor.php';
require __DIR__ . '/ConstantPatcher/Proxy.php';

use Kenjis\MonkeyPatch\Patcher\ConstantPatcher\NodeVisitor;

class ConstantPatcher extends AbstractPatcher
{
	/**
	 * @var string[] special constant names which we don't patch
	 */
	private static $blacklist = [
		'true',
		'false',
		'null',
	];

	public function __construct()
	{
		$this->node_visitor = new NodeVisitor();
	}

	/**
	 * @param string $name constant name
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
}
