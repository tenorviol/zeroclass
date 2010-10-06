<?php

require_once 'lib/autoload.php';

/**
 * Testing the Request_Server class.
 */
class Request_FilterTest extends PHPUnit_Framework_TestCase {
	
	public function impliedFilterProvider() {
		return array(
			array('', ''),
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
		if (is_string($get)) {
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
		if (is_string($get)) {
			$expected = $text === false ? $array['foo'] : $text;
			$this->assertEquals($expected, $result);
		} else {
			$this->assertNull($result);
		}
	}
	
	/**
	 * @dataProvider impliedFilterProvider
	 */
	public function testUtf8_Should_ReturnUtf8ValuesUnchanged($string, $get) {
		$filter = new Request_Filter((array('foo'=>$string)));
		$result = $filter->utf8('foo');
		if (is_string($get)) {
			$this->assertEquals($string, $result);
		} else {
			$this->assertNull($result);
		}
	}
	
	/**
	 * @dataProvider impliedFilterProvider
	 */
	public function testBinary_Should_ReturnValueUnchanged($string) {
		$filter = new Request_Filter((array('foo'=>$string)));
		$result = $filter->binary('foo');
		$this->assertEquals($string, $result);
	}
	
	public function testUnsetOffset_Should_ReturnDefaultValue() {
		$filter = new Request_Filter(array('invalid'=>"\xff"));
		
		// text
		$this->assertEquals(null, $filter->text('foo'));
		$this->assertEquals('bar', $filter->text('foo', 'bar'));
		$this->assertEquals("\xff", $filter->text('foo', "\xff"));    // default not be filtered
		$this->assertEquals('bar', $filter->text('invalid', 'bar'));  // invalid text triggers default
		
		// utf8
		$this->assertEquals('bar', $filter->utf8('foo', 'bar'));
		
		// binary
		$this->assertEquals('bar', $filter->binary('foo', 'bar'));
		
		// boolean
		$this->assertEquals('bar', $filter->boolean('foo', 'bar'));
		
		// boolean
		$this->assertEquals('bar', $filter->int('foo', 'bar'));
	}
	
	public function booleanTests() {
		return array(
			array('on', true),
			array('On', true),
			array('oN', true),
			array('true', true),
			array('1', true),
			array('OFF', false),
			array('false', false),
			array('0', false),
			array('2', false),
			array('2', 'default', 'default'),
		);
	}
	
	/**
	 * @dataProvider booleanTests
	 */
	public function testBoolean_Should_ReturnBooleans($string, $expected, $default = false) {
		$filter = new Request_Filter(array('foo'=>$string));
		$result = $filter->boolean('foo', $default);
		$this->assertEquals($expected, $result);
	}
	
	public function intTests() {
		return array(
			array('1', 1),
			array('-1', -1),
			array('42', 42),
			array('9223372036854775807', 9223372036854775807),
			array('9223372036854775808', 0),
			array('8c', 0),
			array('', 0),
			array('012', 0),  // no octals
		);
	}
	
	/**
	 * @dataProvider intTests
	 */
	public function testInt_Should_ReturnIntegers($string, $expected) {
		$filter = new Request_Filter(array('foo'=>$string));
		$result = $filter->int('foo');
		$this->assertEquals($expected, $result);
	}
	
	public function floatTests() {
		return array(
			array('0', 0.0),
			array('1', 1.0),
			array('4.2', 4.2),
			array('-198748392748397234', -198748392748397234),
			array('5.4e3', 5400),
			array('1000.98', 1000.98),
			array('1,000,000.0001', 0),
			array('', 0),
		);
	}
	
	/**
	 * @dataProvider floatTests
	 */
	public function testFloat_Should_ReturnFloats($string, $expected) {
		$filter = new Request_Filter(array('foo'=>$string));
		$result = $filter->float('foo');
		$this->assertEquals($expected, $result);
	}
	
	public function dateTests() {
		return array(
			array('09/11/01'),
			array('some shit', false)
		);
	}
	
	/**
	 * @dataProvider dateTests
	 */
	public function testApplyStrtotime_Should_ReturnTimestamps($string, $valid=true) {
		$filter = new Request_Filter(array('foo'=>$string));
		$result = $filter->apply('strtotime', 'foo');
		if ($valid) {
			$this->assertEquals(strtotime($string), $result);
		} else {
			$this->assertNull($result);
		}
	}
	
	public function testUnset() {
		$filter = new Request_Filter(array('foo'=>'bar'));
		$this->assertEquals('bar', $filter['foo']);
		unset($filter['foo']);
		$this->assertNull($filter['foo']);
	}
	
	public function emailTests() {
		return array(
			array('foo@foo.com', true),
			array('not an email', false),
			array('@noone.com', false),
			array('nowhere@', false),
			array("\xffbadchar@chars.com", false),
			array('Pelé@example.com', false),  // unicode not supported... yet
			array('"Crios Johnson"@example.com', false),
			array('"Crios+Johnson"@example.com', true),
			array('"Crios+Johnson"@127.0.0.1', false),  // no?
		);
	}
	
	/**
	 * @dataProvider emailTests
	 */
	public function testEmail_Should_ReturnValidEmails($email, $valid) {
		$filter = new Request_Filter(array('email'=>$email));
		$result = $filter->email('email');
		if ($valid) {
			$this->assertEquals($email, $result);
		} else {
			$this->assertNull($result);
		}
	}
}
