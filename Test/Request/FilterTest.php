<?php

require_once __DIR__.'/../base.php';

/**
 * Testing the Request_Server class.
 */
class Test_Request_FilterTest extends PHPUnit_Framework_TestCase {
	
	public function impliedFilterProvider() {
		return array(
			array(array('foo'=>'bar'), 'bar'),
			array(array('foo'=>'bar <script>alert(666);</script>'), 'bar alert(666);'),
			array(array('foo'=>'<b>bar</b>'), 'bar'),
			array(array('foo'=>'bar > foo'), 'bar > foo'),
			array(array('foo'=>"bar < foo\nwhere does it end? >"), 'bar '),
			array(array('foo'=>'Totally ascii string.'), 'Totally ascii string.'),
			array(array('foo'=>'Τὴ γλῶσσα μοῦ ἔδωσαν ἑλληνικὴ'), 'Τὴ γλῶσσα μοῦ ἔδωσαν ἑλληνικὴ'),
			array(array('foo'=>'AÁaáCĆcćEÉeéIÍiíLĹlĺNŃnńOÓoóRŔrŕSŚsśUÚuúYÝyýZŹzź'), 'AÁaáCĆcćEÉeéIÍiíLĹlĺNŃnńOÓoóRŔrŕSŚsśUÚuúYÝyýZŹzź'),
			array(array('foo'=>"bar > foo\xff"), false),
			array(array('foo'=>"\xfe"), false),
		);
	}
	
	/**
	 * @dataProvider impliedFilterProvider
	 */
	public function testArrayShouldReturnHtmlStrippedText($array, $foo) {
		// directly accessing the array should return a filtered value
		$filter = new Request_Filter($array);
		$this->assertEquals($foo, $filter['foo']);
		
		// accessing the array via iterator should also return the filtered value
		$result = iterator_to_array($filter);
		$this->assertEquals($foo, $result['foo']);
	}
	
	/**
	 * @dataProvider impliedFilterProvider
	 */
	public function test2dArrayShouldReturnStrippedText($array, $foo) {
		$array = array('bar'=>$array);
		
		// directly accessing the array should return a filtered value
		$filter = new Request_Filter($array);
		$this->assertEquals($foo, $filter['bar']['foo']);
		
		// accessing the array via iterator should also return the filtered value
		$bar = iterator_to_array($filter['bar']);
		$this->assertEquals($foo, $bar['foo']);
	}
	
	/**
	 * @dataProvider impliedFilterProvider
	 */
	public function testTextShouldReturnValidUtf8WithoutChange($array, $valid) {
		$filter = new Request_Filter($array);
		$result = $filter->text('foo');
		if ($valid) {
			$this->assertEquals($array['foo'], $result);
		} else {
			$this->assertFalse($result);
		}
	}
}
