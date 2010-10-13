<?php

require_once 'lib/autoload.php';

class Controller_RequestMapperTest extends PHPUnit_Framework_TestCase {
	
	public function testPostMap() {
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST['time'] = strval(microtime());
		
		ob_start();
		
		$mapper = new TestMapper();
		$controller = new Controller_RequestMapper($mapper);
		$controller->control();
		
		$result = ob_get_contents();
		ob_end_clean();
		$this->assertEquals("post $_POST[time]", $result);
	}
	
	public function testGetMap() {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_GET['time'] = strval(microtime());
		
		ob_start();
		
		$mapper = new TestMapper();
		$controller = new Controller_RequestMapper($mapper);
		$controller->control();
		
		$result = ob_get_contents();
		ob_end_clean();
		$this->assertEquals("get $_GET[time]", $result);
	}
}

class TestMapper implements Request_Mapper {
	
	public function requestGet(Request_Filter $request) {
		echo "get $request[time]";
	}
	
	public function requestPost(Request_Filter $request) {
		echo "post $request[time]";
	}
}
