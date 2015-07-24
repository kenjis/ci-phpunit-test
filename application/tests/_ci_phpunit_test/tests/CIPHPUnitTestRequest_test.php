<?php

class CIPHPUnitTestRequest_test extends PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException LogicException
	 * @expectedExceptionMessage Status code is not set
	 */
	public function test_getStatus()
	{
		$obj = new CIPHPUnitTestRequest();
		$obj->getStatus();
	}
}
