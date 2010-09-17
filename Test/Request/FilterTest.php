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
			array(array('foo'=>'ÁáĆćÉéÍíĹĺŃńÓóŔŕŚśÚúÝýŹź'), 'ÁáĆćÉéÍíĹĺŃńÓóŔŕŚśÚúÝýŹź'),
			array(array('foo'=>"bar > foo\xff"), null),
			array(array('foo'=>"\xfe"), null),
			array(array('foo'=>"foo\x00\x01\x02\x03\x04\x05\x06\x07bar"), "foobar", "foobar"),
			array(array('foo'=>"foo\x08\x09\x0a\x0b\x0c\x0d\x0e\x0fbar"), "foo\t\n\rbar", "foo\t\n\rbar"),
			array(array('foo'=>"foo\x10\x11\x12\x13\x14\x15\x16\x17bar"), "foobar", "foobar"),
			array(array('foo'=>"foo\x18\x19\x1a\x1b\x1c\x1d\x1e\x1fbar"), "foobar", "foobar"),
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
		
		$result = $filter->get('foo', 'default');
		if ($foo) {
			$this->assertEquals($foo, $result);
		} else {
			$this->assertEquals('default', $result);
		}
	}
	
	/**
	 * @dataProvider impliedFilterProvider
	 */
	public function test2dArrayShouldReturnStrippedText($array, $foo) {
		$array = array('bar'=>$array);
		
		// directly accessing the array should return a filtered value
		$filter = new Request_Filter($array);
		$this->assertEquals($foo, $filter['bar']['foo']);
		
		// test via iterator
		$bar = iterator_to_array($filter['bar']);
		$this->assertEquals($foo, $bar['foo']);
	}
	
	/**
	 * @dataProvider impliedFilterProvider
	 */
	public function testTextShouldReturnValidUtf8WithoutChange($array, $valid, $text = null) {
		$filter = new Request_Filter($array);
		$result = $filter->text('foo');
		if ($valid) {
			$this->assertEquals($text ? $text : $array['foo'], $result);
		} else {
			$this->assertNull($result);
		}
	}
	
	public function testUnsetOffsetShouldReturnDefaultValue() {
		$filter = new Request_Filter(array('invalid'=>"\xff"));
		$this->assertEquals(null, $filter->text('foo'));
		$this->assertEquals('bar', $filter->text('foo', 'bar'));
		$this->assertEquals("\xff", $filter->text('foo', "\xff"));    // default not be filtered
		$this->assertEquals('bar', $filter->text('invalid', 'bar'));  // invalid text triggers default
	}
}
