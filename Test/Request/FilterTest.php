<?php

require_once __DIR__.'/../base.php';

/**
 * Testing the Request_Server class.
 */
class Test_Request_FilterTest extends PHPUnit_Framework_TestCase {
	
	public function impliedFilterProvider() {
		return array(
			array('bar', 'bar'),
			array('bar <script>alert(666);</script>', 'bar alert(666);'),
			array('<b>bar</b>', 'bar'),
			array('bar > foo', 'bar > foo'),
			array("bar < foo\nwhere does it end? >", 'bar '),
			array('Totally ascii string.', 'Totally ascii string.'),
			array('Τὴ γλῶσσα μοῦ ἔδωσαν ἑλληνικὴ', 'Τὴ γλῶσσα μοῦ ἔδωσαν ἑλληνικὴ'),
			array('ÁáĆćÉéÍíĹĺŃńÓóŔŕŚśÚúÝýŹź', 'ÁáĆćÉéÍíĹĺŃńÓóŔŕŚśÚúÝýŹź'),
			array("bar > foo\xff", null),
			array("\xfe", null),
			array("foo\x00\x01\x02\x03\x04\x05\x06\x07bar", "foobar", "foobar"),
			array("foo\x08\x09\x0a\x0b\x0c\x0d\x0e\x0fbar", "foo\t\n\rbar", "foo\t\n\rbar"),
			array("foo\x10\x11\x12\x13\x14\x15\x16\x17bar", "foobar", "foobar"),
			array("foo\x18\x19\x1a\x1b\x1c\x1d\x1e\x1fbar", "foobar", "foobar"),
		);
	}
	
	/**
	 * @dataProvider impliedFilterProvider
	 */
	public function testArrayOffset_Should_ReturnTagStrippedText($string, $get) {
		$array = array('foo'=>$string);
		$filter = new Request_Filter($array);
		
		// directly accessing the array should return a filtered value
		$this->assertEquals($get, $filter['foo']);
		
		// accessing the array via iterator also returns a filtered value
		$result = iterator_to_array($filter);
		$this->assertEquals($get, $result['foo']);
		
		// accessing via get method adds an optional default value
		$result = $filter->get('foo', 'default');
		if ($get) {
			$this->assertEquals($get, $result);
		} else {
			$this->assertEquals('default', $result);
		}
	}
	
	/**
	 * @dataProvider impliedFilterProvider
	 */
	public function test2dArray_Should_ReturnStrippedText($string, $get) {
		$array = array('bar'=>array('foo'=>$string));
		
		// directly accessing the array should return a filtered value
		$filter = new Request_Filter($array);
		$this->assertEquals($get, $filter['bar']['foo']);
		
		// test via iterator
		$bar = iterator_to_array($filter['bar']);
		$this->assertEquals($get, $bar['foo']);
	}
	
	/**
	 * @dataProvider impliedFilterProvider
	 */
	public function testText_Should_ReturnValidUtf8SansControlCodes($string, $get, $text = false) {
		$array = array('foo'=>$string);
		$filter = new Request_Filter($array);
		
		$result = $filter->text('foo');
		if ($get) {
			$expected = $text === false ? $array['foo'] : $text;
			$this->assertEquals($expected, $result);
		} else {
			$this->assertNull($result);
		}
	}
	
	public function testUnsetOffset_Should_ReturnDefaultValue() {
		$filter = new Request_Filter(array('invalid'=>"\xff"));
		$this->assertEquals(null, $filter->text('foo'));
		$this->assertEquals('bar', $filter->text('foo', 'bar'));
		$this->assertEquals("\xff", $filter->text('foo', "\xff"));    // default not be filtered
		$this->assertEquals('bar', $filter->text('invalid', 'bar'));  // invalid text triggers default
	}
}
