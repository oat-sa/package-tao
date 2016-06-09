<?php

use qtism\common\datatypes\Pair;
use qtism\common\datatypes\Duration;
use qtism\common\datatypes\Point;
use qtism\data\storage\Utils;
use qtism\common\enums\BaseType;
use qtism\common\datatypes\Shape;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

class UtilsTest extends QtiSmTestCase {
	
	/**
	 * @dataProvider validIntegerProvider
	 */
	public function testStringToInteger($string, $expected) {
		$value = Utils::stringToDatatype($string, BaseType::INTEGER);
		$this->assertInternalType('integer', $value);
		$this->assertTrue($value === $expected);
	}
	
	/**
	 * @dataProvider invalidIntegerProvider
	 */
	public function testStringToIntegerInvalid($string) {
		$this->setExpectedException('\\UnexpectedValueException');
		$value = Utils::stringToDatatype($string, BaseType::INTEGER);
	}
	
	/**
	 * @dataProvider validFloatProvider
	 */
	public function testStringToFloatValid($string, $expected) {
		$value = Utils::stringToDatatype($string, BaseType::FLOAT);
		$this->assertInternalType('float', $value);
		$this->assertTrue($value === $expected);
	}
	
	/**
	 * @dataProvider invalidFloatProvider
	 */
	public function testStringToFloatInvalid($string) {
		$this->setExpectedException('\\UnexpectedValueException');
		$value = Utils::stringToDatatype($string, BaseType::FLOAT);
	}
	
	/**
	 * @dataProvider validBooleanProvider
	 */
	public function testStringToBooleanValid($string, $expected) {
		$value = Utils::stringToDatatype($string, BaseType::BOOLEAN);
		$this->assertInternalType('boolean', $value);
		$this->assertTrue($expected === $value);
	}
	
	/**
	 * @dataProvider invalidBooleanProvider
	 */
	public function testStringToBooleanInvalid($string) {
		$this->setExpectedException('\\UnexpectedValueException');
		$value = Utils::stringToDatatype($string, BaseType::BOOLEAN);
	}
	
	/**
	 * @dataProvider validPointProvider
	 */
	public function testStringToPointValid($string, $expected) {
		$value = Utils::stringToDatatype($string, BaseType::POINT);
		$this->assertInternalType('integer', $value->getX());
		$this->assertInternalType('integer', $value->getY());
		$this->assertEquals($expected->getX(), $value->getX());
		$this->assertEquals($expected->getY(), $value->getY());
	}
	
	/**
	 * @dataProvider invalidPointProvider
	 */
	public function testStringToPointInvalid($string) {
		$this->setExpectedException('\\UnexpectedValueException');
		$value = Utils::stringToDatatype($string, BaseType::POINT);
	}
	
	/**
	 * @dataProvider validDurationProvider
	 */
	public function testStringToDurationValid($string, $expected) {
		$value = Utils::stringToDatatype($string, BaseType::DURATION);
		$this->assertInstanceOf('qtism\\common\\datatypes\\Duration', $value);
		$this->assertEquals($value->getDays(), $expected->getDays());
		$this->assertEquals($value->getYears(), $expected->getYears());
		$this->assertEquals($value->getHours(), $expected->getHours());
		$this->assertEquals($value->getMinutes(), $expected->getMinutes());
		$this->assertEquals($value->getMonths(), $expected->getMonths());
		$this->assertEquals($value->getSeconds(), $expected->getSeconds());
	}
	
	/**
	 * @dataProvider invalidDurationProvider
	 */
	public function testStringToDurationInvalid($string) {
		$this->setExpectedException('\\UnexpectedValueException');
		$value = Utils::stringToDatatype($string, BaseType::DURATION);
	}
	
	/**
	 * @dataProvider validPairProvider
	 */
	public function testStringToPairValid($string, $expected) {
		$value = Utils::stringToDatatype($string, BaseType::PAIR);
		$this->assertInstanceOf('qtism\\common\\datatypes\\Pair', $value);
		$this->assertEquals($expected->getFirst(), $value->getFirst());
		$this->assertEquals($expected->getSecond(), $value->getSecond());
	}
	
	/**
	 * @dataProvider invalidPairProvider
	 */
	public function testStringToPairInvalid($string) {
		$this->setExpectedException('\\UnexpectedValueException');
		$value = Utils::stringToDatatype($string, BaseType::PAIR);
	}
	
	/**
	 * @dataProvider validPairProvider
	 */
	public function testStringToDirectedPairValid($string, $expected) {
		$value = Utils::stringToDatatype($string, BaseType::DIRECTED_PAIR);
		$this->assertInstanceOf('qtism\\common\\datatypes\\DirectedPair', $value);
		$this->assertEquals($expected->getFirst(), $value->getFirst());
		$this->assertEquals($expected->getSecond(), $value->getSecond());
	}
	
	/**
	 * @dataProvider invalidPairProvider
	 */
	public function testStringToDirectedPairInvalid($string) {
		$this->setExpectedException('\\UnexpectedValueException');
		$value = Utils::stringToDatatype($string, BaseType::PAIR);
	}
	
	/**
	 * @dataProvider validCoordsProvider
	 */
	public function testStringToCoords($string, $shape) {
		$coords = Utils::stringToCoords($string, $shape);
		$this->assertInstanceOf('qtism\\common\\datatypes\\Coords', $coords);
		
		$intCoords = explode(",", $string);
		$this->assertEquals(count($intCoords), count($coords));
		
		for ($i = 0; $i < count($intCoords); $i++) {
			$this->assertEquals(intval($intCoords[$i]), $coords[$i]);
		}
	}
	
	/**
	 * @dataProvider invalidCoordsProvider
	 */
	public function testStringToCoordsInvalid($string, $shape) {
		$this->setExpectedException('\\UnexpectedValueException');
		$coords = Utils::stringToCoords($string, $shape);
	}
	
	/**
	 * @dataProvider invalidShapeProvider
	 */
	public function testStringToCoordsInvalidShapes($string, $shape) {
		$this->setExpectedException('\\InvalidArgumentException');
		$coords = Utils::stringToCoords($string, $shape);
	}
	
	/**
	 * @dataProvider validUriToSanitizeProvider
	 */
	public function testValidUriToSanitize($uri, $expected) {
		$this->assertEquals($expected, Utils::sanitizeUri($uri));
	}
	
	/**
	 * @dataProvider invalidUriToSanitizeProvider
	 */
	public function testInvalidUriToSanitize($uri) {
		$this->setExpectedException('\\InvalidArgumentException');
		$uri = Utils::sanitizeUri($uri);
	}
	
	public function validCoordsProvider() {
		return array(
			array('30, 30, 60, 30', Shape::RECT),
			array('10, 10, 10', Shape::CIRCLE),
			array('10,10,10', Shape::CIRCLE),
			array('0,8,7,4,2,2,8,-4,-2,1', Shape::POLY),
		    array('30.1, 30, 50, 30.1', Shape::RECT),
		    array('184,237,18.38', Shape::CIRCLE),
		    array('-184 ,237, -18.38', Shape::CIRCLE)
		);
	}
	
	public function invalidCoordsProvider() {
		return array(
			array('invalid', SHAPE::RECT),
			array('20;40;30', SHAPE::CIRCLE),
		    array('184.456,237.,18', SHAPE::CIRCLE),
		);
	}
	
	public function invalidShapeProvider() {
		return array(
			array('10, 10, 10', SHAPE::DEF),
			array('10', 25)
		);
	}
	
	public function validIntegerProvider() {
		return array(
			array('25', 25),
			array(' 25', 25),
			array('25 ', 25),
			array('0', 0),
			array('-0', 0),
			array('-150', -150),
			array(' -150', -150),
			array('-150 ', -150)
		);
	}
	
	public function invalidIntegerProvider() {
		return array(
			array('25.234'),
			array('A B'),
			array('-'),
			array('+'),
			array('abcd'),
			array('-bd'),
			array(null)
		);
	}
	
	public function validFloatProvider() {
		return array(
			array('25.234', 25.234),
			array('25', floatval(25)),
			array('-25', -floatval(25)),
			array('-25.234', -25.234),
			array('25.0', 25.0)		
		);
	}
	
	public function invalidFloatProvider() {
		return array(
				array('2a'),
				array('A B'),
				array('-'),
				array('+'),
				array('abcd'),
				array('-bd'),
				array(null)
		);
	}
	
	public function validBooleanProvider() {
		return array(
			array('true', true),
			array('false', false),
			array('  true', true),
			array('false ', false)		
		);
	}
	
	public function invalidBooleanProvider() {
		return array(
			array('tru'),
			array(''),
			array('f'),
			array(null),
			array(24)		
		);
	}
	
	public function validPointProvider() {
		return array(
			array('20 30', new Point(20, 30)),
			array('240 30', new Point(240, 30)),
			array('-10 3', new Point(-10, 3))
		);
	}
	
	public function invalidPointProvider() {
		return array(
			array('20 x'),
			array('x  y'),
			array('xy'),
			array('x y'),
			array('20px 20em'),
			array('20'),
			array(''),
			array(null)
		);
	}
	
	public function validDurationProvider() {
		return array(
			array('P1D', new Duration('P1D')), // 1 day
			array('P2W', new Duration('P2W')), // 2 weeks
			array('P3M', new Duration('P3M')), // 3 months
			array('P4Y', new Duration('P4Y')), // 4 years
			array('P1Y1D', new Duration('P1Y1D')), // 1 year + 1 day
			array('P1DT12H', new Duration('P1DT12H')), // 1 day + 12 hours
			array('PT3600S', new Duration('PT3600S')) // 3600 seconds
		);
	}
	
	public function invalidDurationProvider() {
		return array(
			array('D1P'),
			array('3600'),
			array(''),
			array('abcdef'),
			array(null)		
		);
	}
	
	public function validPairProvider() {
		return array(
			array('Bidule Trucmuche', new Pair('Bidule', 'Trucmuche')),
			array('C D', new Pair('C', 'D'))
		);
	}
	
	public function invalidPairProvider() {
		return array(
			array('Machinbrol'),
			array('bidule 0'),
			array(''),
			array(null)		
		);
	}
	
	public function validUriToSanitizeProvider() {
		return array(
			array('http://www.taotesting.com/', 'http://www.taotesting.com'),
			array('', ''),
			array('http://taotesting.com', 'http://taotesting.com'),
			array('./', '.'),
			array('../', '..'),
			array('/../../q01.xml', '/../../q01.xml'),
			array('./../../q01.xml/', './../../q01.xml'),
			array('/', '')		
		);
	}
	
	public function invalidUriToSanitizeProvider() {
		return array(
			array(new stdClass()),
			array(14),
			array(true)
		);
	}
}
