<?php

use qtism\common\datatypes\Boolean;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\String;
use qtism\common\Comparable;
use qtism\runtime\common\Container;
use qtism\common\datatypes\DirectedPair;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\Point;
use qtism\runtime\common\OrderedContainer;
use qtism\common\datatypes\Duration;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\RecordContainer;
use qtism\runtime\common\Utils;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');


class RuntimeUtilsTest extends QtiSmTestCase {

	/**
	 * @dataProvider inferBaseTypeProvider
	 */
	public function testInferBaseType($value, $expectedBaseType) {
		$this->assertTrue(Utils::inferBaseType($value) === $expectedBaseType);
	}
	
	/**
	 * @dataProvider inferCardinalityProvider
	 */
	public function testInferCardinality($value, $expectedCardinality) {
		$this->assertTrue(Utils::inferCardinality($value) === $expectedCardinality);
	}
	
	/**
	 * @dataProvider isValidVariableIdentifierProvider
	 * 
	 * @param string $string
	 * @param boolean $expected
	 */
	public function testIsValidVariableIdentifier($string, $expected) {
		$this->assertSame($expected, Utils::isValidVariableIdentifier($string));
	}
	
	public function inferBaseTypeProvider() {
		$returnValue = array();
		
		$returnValue[] = array(new RecordContainer(), false);
		$returnValue[] = array(new RecordContainer(array('a' => new Integer(1), 'b' => new Integer(2))), false);
		$returnValue[] = array(null, false);
		$returnValue[] = array(new String(''), BaseType::STRING);
		$returnValue[] = array(new String('String!'), BaseType::STRING);
		$returnValue[] = array(new Boolean(false), BaseType::BOOLEAN);
		$returnValue[] = array(new Integer(0), BaseType::INTEGER);
		$returnValue[] = array(new Float(0.0), BaseType::FLOAT);
		$returnValue[] = array(new MultipleContainer(BaseType::DURATION), BaseType::DURATION);
		$returnValue[] = array(new OrderedContainer(BaseType::BOOLEAN), BaseType::BOOLEAN);
		$returnValue[] = array(new Duration('P1D'), BaseType::DURATION);
		$returnValue[] = array(new Point(1, 1), BaseType::POINT);
		$returnValue[] = array(new Pair('A', 'B'), BaseType::PAIR);
		$returnValue[] = array(new DirectedPair('A', 'B'), BaseType::DIRECTED_PAIR);
		$returnValue[] = array(new \StdClass(), false);
		$returnValue[] = array(new Container(), false);
		
		return $returnValue;
	}
	
	public function inferCardinalityProvider() {
		$returnValue = array();
		
		$returnValue[] = array(new RecordContainer(), Cardinality::RECORD);
		$returnValue[] = array(new MultipleContainer(BaseType::INTEGER), Cardinality::MULTIPLE);
		$returnValue[] = array(new OrderedContainer(BaseType::DURATION), Cardinality::ORDERED);
		$returnValue[] = array(new \stdClass(), false);
		$returnValue[] = array(null, false);
		$returnValue[] = array(new String(''), Cardinality::SINGLE);
		$returnValue[] = array(new String('String!'), Cardinality::SINGLE);
		$returnValue[] = array(new Integer(0), Cardinality::SINGLE);
		$returnValue[] = array(new Float(0.0), Cardinality::SINGLE);
		$returnValue[] = array(new Boolean(false), Cardinality::SINGLE);
		$returnValue[] = array(new Point(1, 1), Cardinality::SINGLE);
		$returnValue[] = array(new Pair('A', 'B'), Cardinality::SINGLE);
		$returnValue[] = array(new DirectedPair('A', 'B'), Cardinality::SINGLE);
		$returnValue[] = array(new Duration('P1D'), Cardinality::SINGLE);
		
		return $returnValue;
	}
	
	public function isValidVariableIdentifierProvider() {
		return array(
			array('Q01', true),
			array('Q_01', true),
			array('Q-01', true),
			array('Q*01', false),
			array('q01', true),
			array('_Q01', false),
			array('', false),
			array(1337, false),
			array('Q01.1', true),
			array('Q01.1.SCORE', true),
			array('Q01.999.SCORE', true),
			array('Q01.A.SCORE', false),
			array('Qxx.12.', false),
			array('Q-2.', false),
			array('934.9.SCORE', false),
			array('A34.10.S-C-O', true),
			array('999', false),
			array('Q01.1.SCORE.MAX', false),
			array('Q 01', false),
			array('Q01 . SCORE', false),
			array('Q_01.SCORE', true),
			array('Q01.0.SCORE', false), // non positive sequence number -> false
			array('Q01.09.SCORE', false) // prefixing sequence by zero not allowed.
		);
	}
}