<?php

class CIPHPUnitTestExitPatcher_test extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider provide_source
	 */
	public function test_die($source, $expected)
	{
		list($actual,) = CIPHPUnitTestExitPatcher::patch($source);
		$this->assertEquals($expected, $actual);
	}

	public function provide_source()
	{
		return [
[<<<'EOL'
<?php
die();
EOL
,
<<<'EOL'
<?php
exit_();
EOL
],
[<<<'EOL'
<?php
die;
EOL
,
<<<'EOL'
<?php
exit_();
EOL
],
[<<<'EOL'
<?php
exit();
EOL
,
<<<'EOL'
<?php
exit_();
EOL
],
[<<<'EOL'
<?php
exit;
EOL
,
<<<'EOL'
<?php
exit_();
EOL
],
[<<<'EOL'
<?php
exit('status');
EOL
,
<<<'EOL'
<?php
exit_('status');
EOL
],
		];
	}
}
