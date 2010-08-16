<?php

require_once __DIR__.'/../base.php';

/**
 * Testing the Request_Server class.
 */
class Test_Request_Filter extends PHPUnit_Framework_TestCase {
	
	public function impliedFilterProvider() {
		return array(
			array(new Request_Filter(array('foo'=>'bar')), 'bar'),
			array(new Request_Filter(array('foo'=>'bar <script>alert(666);</script>')), 'bar alert(666);'),
			array(new Request_Filter(array('foo'=>'<b>bar</b>')), 'bar'),
			array(new Request_Filter(array('foo'=>'bar > foo')), 'bar > foo'),
			array(new Request_Filter(array('foo'=>"bar < foo\nwhere does it end? >")), 'bar '),
			array(new Request_Filter(array('foo'=>1)), 1),
			array(new Request_Filter(array('foo'=>false)), false),
			array(new Request_Filter(array('foo'=>42.395)), 42.395),
			array(new Request_Filter(array('foo'=>"bar > foo\xff")), false),
		);
	}
	
	/**
	 * @dataProvider impliedFilterProvider
	 */
	public function testArrayAccessShouldReturnFilteredResultDevoidOfHtml($array, $expected) {
		// directly accessing the array should return a filtered value
		$result = $array['foo'];
		$this->assertEquals($expected, $result);
		
		// accessing the array via iterator should also return the filtered value
		$tested = false;
		foreach ($array as $key => $value) {
			if ($key == 'foo') {
				$this->assertEquals($expected, $value);
				$tested = true;
			}
		}
		$this->assertTrue($tested);
	}
	
	public function arrayFilterProvider() {
		return array(
			array(new Request_Filter(array('foo'=>array('bar'=>'fubar'))), 'fubar'),
			array(new Request_Filter(array('foo'=>array('bar'=>'fubar <script>alert(666);</script>'))), 'fubar alert(666);'),
			array(new Request_Filter(array('foo'=>array('bar'=>'<b>fubar</b>'))), 'fubar'),
			array(new Request_Filter(array('foo'=>array('bar'=>'fubar > foo'))), 'fubar > foo'),
			array(new Request_Filter(array('foo'=>array('bar'=>"fubar < foo\nwhere does it end? >"))), 'fubar '),
			array(new Request_Filter(array('foo'=>array('bar'=>1))), 1),
			array(new Request_Filter(array('foo'=>array('bar'=>false))), false),
			array(new Request_Filter(array('foo'=>array('bar'=>42.395))), 42.395),
		);
	}
	
	/**
	 * @dataProvider arrayFilterProvider
	 */
	public function testMultiDimensionalArrayAccessShouldAlsoReturnFilteredResults($array, $expected) {
		// directly accessing the array should return a filtered value
		$result = $array['foo']['bar'];
		$this->assertEquals($expected, $result);
		
		// accessing the array via iterator should also return the filtered value
		$tested = false;
		foreach ($array['foo'] as $key => $value) {
			if ($key == 'bar') {
				$this->assertEquals($expected, $value);
				$tested = true;
			}
		}
		$this->assertTrue($tested);
	}
	
	public function validUtf8Provider() {
		return array(
			array('Totally ascii string.'),
			array('Τὴ γλῶσσα μοῦ ἔδωσαν ἑλληνικὴ'),
			array('AÁaáCĆcćEÉeéIÍiíLĹlĺNŃnńOÓoóRŔrŕSŚsśUÚuúYÝyýZŹzź'),
		);
	}
	
	/**
	 * @dataProvider validUtf8Provider
	 */
	public function testTextShouldReturnValidUtf8WithoutChange($text) {
		$request = new Request_Filter(array('foo'=>$text));
		$result = $request->text('foo');
		$this->assertEquals($text, $result);
	}
	
	public function invalidUtf8Provider() {
		return array(
			array("\xff"),
			array("\xfe"),
		);
	}
	
	/**
	 * @dataProvider invalidUtf8Provider
	 */
	public function testTextShouldReturnFalseInsteadOfInvalidUtf8($text) {
		$request = new Request_Filter(array('foo'=>$text));
		$result = $request->text('foo');
		$this->assertFalse($result);
	}
	
	// TODO: more filters: raw, utf-8, typed (i.e. boolean, int, etc), default values
}
