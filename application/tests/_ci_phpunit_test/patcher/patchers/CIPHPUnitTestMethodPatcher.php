<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

require __DIR__ . '/CIPHPUnitTestMethodPatcher/CIPHPUnitTestMethodPatcherNodeVisitor.php';
require __DIR__ . '/CIPHPUnitTestMethodPatcher/CIPHPUnitTestMethodPatchManager.php';

const __GO_ORIG_METHOD__ = '__GO_ORIG_METHOD__';

class CIPHPUnitTestMethodPatcher
{
	const CODE = <<<'EOL'
if (($__ret__ = CIPHPUnitTestMethodPatchManager::getReturn(__CLASS__, __FUNCTION__, func_get_args())) !== __GO_ORIG_METHOD__) return $__ret__;
EOL;

	public static $replacement;

	public static function patch($source)
	{
		$patched = false;
		self::$replacement = [];

		$parser = new PhpParser\Parser(new PhpParser\Lexer(
			['usedAttributes' => ['startTokenPos', 'endTokenPos']]
		));
		$traverser = new PhpParser\NodeTraverser;
		$traverser->addVisitor(new CIPHPUnitTestMethodPatcherNodeVisitor());

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

		$start_method = false;

		foreach ($tokens as $token)
		{
			$i++;

			if ($i == $replacement['key'])
			{
				$start_method = true;
			}

			if (is_string($token))
			{
				if ($start_method && $token === '{')
				{
					$new_source .= '{ ' . self::CODE;
					$start_method = false;
					$replacement = each(self::$replacement);
				}
				else
				{
					$new_source .= $token;
				}
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
