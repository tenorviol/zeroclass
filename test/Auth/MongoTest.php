<?php

require_once 'lib/autoload.php';

class Auth_MongoTest extends PHPUnit_Framework_TestCase {
	
	private function collection() {
		$mongo = new Mongo();
		return $mongo->test->selectCollection(__CLASS__);
	}
	
	public function setUp() {
		$this->collection()->drop();
	}
	
	public function test() {
		$auth = new Auth_Mongo($this->collection());
		
		$auth->setPassword(1, 'foo');
		$auth->setPassword(2, 'bar');
		
		$auth->login(1, 'foo');
		$this->assertEquals(1, $auth->userId());
		
		$auth->logout();
		$this->assertNull($auth->userId());
		
		try {
			$e = null;
			$auth->login(1, 'bar');  // invalid login
		} catch (Auth_Exception $e) {
			$this->assertNull($auth->userId());
		}
		$this->assertType('Auth_Exception', $e);
		
		$auth->deletePassword(1);
		
		try {
			$e = null;
			$auth->login(1, 'foo');  // no longer a user
		} catch (Auth_Exception $e) {
			$this->assertNull($auth->userId());
		}
		$this->assertType('Auth_Exception', $e);
		
		$auth->login(2, 'bar');  // should still be a user
		$this->assertEquals(2, $auth->userId());
		
		$auth->logout();
		$this->assertNull($auth->userId());
		
		$auth->setPassword(2, 'fubar');
		
		$auth->login(2, 'fubar');
		$this->assertEquals(2, $auth->userId());
	}
}
