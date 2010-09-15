<?php

require_once __DIR__.'/../base.php';

class Test_Request_IteratorTest extends PHPUnit_Framework_TestCase {
	public function test() {
		
		$array = array(
			'foo'=>'bar',
			'john'=>'lennon',
			'paul'=>'mccartney',
			'ringo'=>'star',
			'george'=>'harrison',
		);
		
		$iterator = new Request_Iterator($array, array_keys($array));
		
		$result = array();
		foreach ($iterator as $key => $value) {
			$result[$key] = $value;
		}
		
		$this->assertEquals($array, $result);
	}
}
