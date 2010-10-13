<?php

require_once 'lib/autoload.php';

class ContainerTest extends PHPUnit_Framework_TestCase {
	
	public function testPropertyReturnsPremadeAndManufacturedData() {
		$container = new TestContainer();
		
		// simple set and retrieve
		$container->foo = 'bar';
		$this->assertEquals('bar', $container->foo);
		
		// implicitly call createX method
		$this->assertFalse(isset($container->test));
		$this->assertEquals('Hello world!', $container->test);
		$this->assertTrue(isset($container->test));
	}
	
	/**
	 * @expectedException NotFoundException
	 */
	public function testPropertyWithoutFactoryThrowsNotFoundException() {
		$container = new Container();
		$container->john;
	}
	
	/**
	 * @expectedException NotFoundException
	 */
	public function testInstancePropertyShouldNotTriggerInfiniteLoop() {
		$container = new Container();
		$this->assertNull($container->instance);
	}
	
	/**
	 * @expectedException Exception
	 */
	public function testUndefinedMethodThrowsException() {
		$container = new Container();
		$container->jacob();
	}
}

class TestContainer extends Container {
	public function createTest() {
		return 'Hello world!';
	}
}
