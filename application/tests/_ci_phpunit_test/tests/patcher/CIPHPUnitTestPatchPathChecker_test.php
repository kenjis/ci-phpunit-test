<?php

class CIPHPUnitTestPatchPathChecker_test extends PHPUnit_Framework_TestCase
{
	public static function tearDownAfterClass()
	{
		CIPHPUnitTestPatchPathChecker::setWhitelistDir(
			[
				APPPATH,
			]
		);
		CIPHPUnitTestPatchPathChecker::setBlacklistDir(
			[
				realpath(APPPATH . '../vendor/'),
				APPPATH . 'tests/',
			]
		);
	}

	public function test_check_true()
	{
		CIPHPUnitTestPatchPathChecker::setWhitelistDir(
			[
				'/abc/def',
			]
		);
		$test = CIPHPUnitTestPatchPathChecker::check('/abc/def/xyz');
		$this->assertTrue($test);
	}

	public function test_check_false()
	{
		CIPHPUnitTestPatchPathChecker::setBlacklistDir(
			[
				'/abc/def/xyz',
			]
		);
		$test = CIPHPUnitTestPatchPathChecker::check('/abc/def/xyz/123');
		$this->assertFalse($test);
	}
}
