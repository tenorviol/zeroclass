<?php

require_once 'lib/autoload.php';

class RequestTest extends PHPUnit_Framework_TestCase {
	
	public function test() {
		$_COOKIE['foo'] = 'bar';
		$_ENV['foo'] = 'bar';
		$_GET['foo'] = 'bar';
		$_POST['foo'] = 'bar';
		$_REQUEST['foo'] = 'bar';
		$_SERVER['foo'] = 'bar';
		
		$this->assertEquals($_GET['foo'], Request::$_COOKIE->utf8('foo'));
		$this->assertEquals($_GET['foo'], Request::$_ENV->utf8('foo'));
		$this->assertEquals($_GET['foo'], Request::$_GET->utf8('foo'));
		$this->assertEquals($_GET['foo'], Request::$_POST->utf8('foo'));
		$this->assertEquals($_GET['foo'], Request::$_REQUEST->utf8('foo'));
		$this->assertEquals($_GET['foo'], Request::$_SERVER->utf8('foo'));
	}
}
