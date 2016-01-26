<?php
require_once (dirname(__FILE__) . '/../../QtiSmTestCase.php');

use qtism\common\enums\BaseType;

class BaseTypeTest extends QtiSmTestCase {
	
	/**
	 * @dataProvider validBaseTypeProvider
	 */
	public function testGetConstantByNameValidBaseType($baseType) {
		$this->assertInternalType('integer', BaseType::getConstantByName($baseType));
	}
	
	/**
	 * @dataProvider invalidBaseTypeProvider
	 */
	public function testGetConstantByNameInvalidBaseType($baseType) {
		$this->assertFalse(BaseType::getConstantByName($baseType));
	}
	
	/**
	 * @dataProvider validBaseTypeConstantProvider
	 */
	public function testGetNameByConstantValidBaseType($constant, $expected) {
		$this->assertEquals($expected, BaseType::getNameByConstant($constant));
	}
	
	/**
	 * @dataProvider invalidBaseTypeConstantProvider
	 */
	public function testGetNameByConstantInvalidBaseType($constant) {
		$this->assertFalse(BaseType::getNameByConstant($constant));
	}
	
	public function validBaseTypeConstantProvider() {
		return array(
			array(BaseType::IDENTIFIER, 'identifier'),
			array(BaseType::BOOLEAN, 'boolean'),
			array(BaseType::INTEGER, 'integer'),
			array(BaseType::STRING, 'string'),
			array(BaseType::FLOAT, 'float'),
			array(BaseType::POINT, 'point'),
			array(BaseType::PAIR, 'pair'),
			array(BaseType::DIRECTED_PAIR, 'directedPair'),
			array(BaseType::DURATION, 'duration'),
			array(BaseType::FILE, 'file'),
			array(BaseType::URI, 'uri'),
			array(BaseType::INT_OR_IDENTIFIER, 'intOrIdentifier')
		);
	}
	
	public function invalidBaseTypeConstantProvider() {
		return array(
			array(-1)
		);
	}
	
	public function validBaseTypeProvider() {
		return array(
			array('identifier'),
			array('boolean'),
			array('integer'),
			array('string'),
			array('float'),
			array('point'),
			array('pair'),
			array('directedPair'),
			array('duratioN'), // case insensitive function
			array('file'),
			array('uri'),
			array('intOrIdentifier')
		);
	}
	
	public function invalidBaseTypeProvider() {
		return array(
			array(10),
			array('unknown'),
			array('int_or_identifier')
		);
	}
}