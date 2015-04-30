<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\runtime\expressions\operators\Utils as OperatorsUtils;

class OperatorsUtilsTest extends QtiSmTestCase {
	
	/**
	 * @dataProvider gcdProvider
	 * 
	 * @param integer $a
	 * @param integer $b
	 * @param integer $expected
	 */
	public function testGcd($a, $b, $expected) {
		$result = OperatorsUtils::gcd($a, $b);
		$this->assertInternalType('integer', $result);
		$this->assertSame($expected, $result);
	}
	
	/**
	 * @dataProvider lcmProvider
	 * 
	 * @param integer $a
	 * @param integer $b
	 * @param integer $expected
	 */
	public function testLcm($a, $b, $expected) {
		$result = OperatorsUtils::lcm($a, $b);
		$this->assertInternalType('integer', $result);
		$this->assertSame($expected, $expected);
	}
	
	/**
	 * @dataProvider meanProvider
	 * 
	 * @param array $sample
	 * @param number $expected
	 */
	public function testMean(array $sample, $expected) {
		$result = OperatorsUtils::mean($sample);
		$this->assertSame($expected, $result);
	}
	
	/**
	 * @dataProvider varianceProvider
	 * 
	 * @param array $sample
	 * @param boolean Apply Bessel's correction?
	 * @param number $expected
	 */
	public function testVariance(array $sample, $correction, $expected) {
		$result = OperatorsUtils::variance($sample, $correction);
		$this->assertSame($expected, $result);
	}
	
	/**
	 * @dataProvider standardDeviationProvider
	 * 
	 * @param array $sample
	 * @param boolean Apply Bessel's standard correction?
	 * @param number $expected
	 */
	public function testStandardDeviation(array $sample, $correction, $expected) {
		$result = OperatorsUtils::standardDeviation($sample, $correction);
		
		if (is_bool($expected)) {
			$this->assertSame($expected, $result);
		}
		else {
			$this->assertSame($expected, round($result, 2));
		}
	}
	
	/**
	 * @dataProvider getPrecedingBackslashesCountProvider
	 *
	 * @param string $string
	 * @param integer $offset
	 * @param integer $expected Expected preceding backslashes count.
	 */
	public function testGetPrecedingBackslashesCount($string, $offset, $expected) {
		$this->assertSame($expected, OperatorsUtils::getPrecedingBackslashesCount($string, $offset));
	}
	
	/**
	 * @dataProvider pregAddDelimiterProvider
	 *
	 * @param string $string
	 * @param string $expected
	 */
	public function testPregAddDelimiter($string, $expected) {
		$this->assertSame($expected, OperatorsUtils::pregAddDelimiter($string));
	}
	
	/**
	 * @dataProvider escapeSymbolsProvider
	 * 
	 * @param string $string
	 * @param array|string $symbols
	 * @param string $expected
	 */
	public function testEscapeSymbols($string, $symbols, $expected) {
		$this->assertSame($expected, OperatorsUtils::escapeSymbols($string, $symbols));
	}
	
	/**
	 * @dataProvider validCustomOperatorClassToPhpClassProvider
	 * 
	 * @param string $customClass
	 * @param string $expected
	 */
	public function testValidCustomOperatorClassToPhpClass($customClass, $expected) {
	    $this->assertEquals($expected, OperatorsUtils::customOperatorClassToPhpClass($customClass));
	}
	
	/**
	 * @dataProvider invalidCustomOperatorClassToPhpClassProvider
	 * 
	 * @param string $customClass
	 */
	public function testInvalidCustomOperatorClassToPhpClass($customClass) {
	    $this->assertFalse(OperatorsUtils::customOperatorClassToPhpClass($customClass));
	}
	
	public function pregAddDelimiterProvider() {
		return array(
				array('', '//'),
				array('test', '/test/'),
				array('te/st', '/te\\/st/'),
				array('/', '/\\//'),
				array('/test', '/\\/test/'),
				array('test/', '/test\\//'),
				array('te/st is /test/', '/te\\/st is \\/test\\//'),
				array('te\\/st', '/te\\/st/'),
				array('te\\\\/st', '/te\\\\\\/st/'),
				array('te\\\\\\\\/st', '/te\\\\\\\\\\/st/'),
				array('\d{1,2}', '/\d{1,2}/')
		);
	}
	
	public function escapeSymbolsProvider() {
		return array(
			array('10$ are 10$', array('$', '^'), '10\\$ are 10\\$'),
			array('$$$Jackpot$$$', '$', '\\$\\$\\$Jackpot\\$\\$\\$'),
			array('^exp$', array('$', '^'), '\\^exp\\$')
		);
	}
	
	public function getPrecedingBackslashesCountProvider() {
		return array(
				array('', 0, 0),
				array('string!', 0, 0),
				array('string!', 10, 0),
				array('string!', 6, 0),
				array('string!', -20, 0),
				array('\\a', 1, 1),
				array('\\\\a', 2, 2),
				array('\\abc\\\\\\d', 7, 3)
		);
	}
	
	public function gcdProvider() {
		return array(
			array(60, 330, 30),
			array(256, 1024, 256),
			array(456, 3698, 2),
			array(25, 0, 25),
			array(0, 25, 25),
			array(0, 0, 0)
		);
	}
	
	public function lcmProvider() {
		return array(
			array(4, 3, 12),
			array(0, 3, 0),
			array(3, 0, 0),
			array(0, 0, 0),
			array(330, -65, 4290)
		);
	}
	
	public function meanProvider() {
		return array(
			array(array(), false),
			array(array('string!'), false),
			array(array(10, 11, 'string'), false),
			array(array(10, null, 11), false),
			array(array(10, 11, new \stdClass()), false),
			array(array(10), 10),
			array(array(10, 10), 10),
			array(array(10, 20, 30), 20),
			array(array(10.0, 20.0, 30.0), 20.0),
			array(array(0), 0),
			array(array(0, 0), 0)
		);
	}
	
	public function varianceProvider() {
		return array(
			// [0] = sample; [1] = Bessel's correction, [2] = expected result
			array(array(600, 470, 170, 430, 300), false, 21704), // on population (no correction)
			array(array(10), false, 0),
			array(array(600, 470, 170, 430, 300), true, 27130), // on sample (correction)
			array(array('String!'), false, false),
			array(array(null, 10), false, false),
				
			// fails because when using Bessel's correction,
			// the contain size must be > 1
			array(array(10), true, false)
		);
	}
	
	public function standardDeviationProvider() {
		// The equality test will be done with 2 significant figures.
		return array(
			array(array(600, 470, 170, 430, 300), false, 147.32), // on population (no correction)
			array(array(600, 470, 170, 430, 300), true, 164.71), // on sample (correction)
			array(array(10, 'String!'), false, false),
			array(array(0, 0, 0), false, 0.00),
				
			// fails because when using the Bessel's correction,
			// the container size must be > 1
			array(array(10), true, false) 
		);
	}
	
	public function validCustomOperatorClassToPhpClassProvider() {
	    return array(
	        array('com.taotesting.operators.custom.explode', "com\\taotesting\\operators\\custom\\Explode"),
	        array('org.imsglobal.rStats', "org\\imsglobal\\RStats"),
	        array('taotesting.Custom', "taotesting\\Custom"),
	    );
	}
	
	public function invalidCustomOperatorClassToPhpClassProvider() {
	    return array(
	        array('taotesting'),
	        array('com#taotesting'),
	        array(''),
	        array('com|taotesting.custom')
	    );
	}
}