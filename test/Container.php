<?php

require_once __DIR__.'/base.php';

class Test_Container extends PHPUnit_Framework_TestCase {
	
	public function testGetShouldReturnPremeditatedOrManufacturedData() {
		$container = new TestContainer();
		
		// simple set and retrieve
		$container->foo = 'bar';
		$this->assertEquals('bar', $container->foo);
		
		// implicitly call createX method
		$this->assertFalse(isset($container->test));
		$this->assertEquals('Hello world!', $container->test);
		$this->assertTrue(isset($container->test));
	}
	
	public function testPropertyWithoutFactoryShouldReturnNull() {
		error_reporting(0);
		$container = new Container();
		$this->assertNull($container->john);
	}
	
	public function testInstancePropertyShouldNotTriggerInfiniteLoop() {
		error_reporting(0);
		$container = new Container();
		$this->assertNull($container->instance);
	}
	
	/**
	 * This is more implementation detail than feature.
	 * @expectedException Exception
	 */
	public function testUndefinedMethodShouldThrowException() {
		$container = new Container();
		$container->jacob();
	}
}

class TestContainer extends Container {
	public function createTest() {
		return 'Hello world!';
	}
}
