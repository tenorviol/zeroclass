<?php

require_once 'lib/autoload.php';

class RandomTest extends PHPUnit_Framework_TestCase {
	
	protected function getRandom() {
		return new Random_DevUrandom();
	}
	
	public function test() {
		$random = $this->getRandom();
		
		$i1 = $random->int();
		$this->assertType('int', $i1);
		
		for ($x = 0; $x < 5; $x++) {
			$i2 = $random->int();
			if ($i2 != $i1) break;
		}
		$this->assertNotEquals($i1, $i2);
	}
}
