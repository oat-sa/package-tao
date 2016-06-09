<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\runtime\common\VariableIdentifier;

class VariableIdentifierTest extends QtiSmTestCase {
	
	/**
	 * @dataProvider invalidIdentifierProvider
	 * 
	 * @param string $identifier
	 */
	public function testInvalidIdentifier($identifier) {
		$this->setExpectedException('\\InvalidArgumentException');
		$v = new VariableIdentifier($identifier);
	}
	
	/**
	 * @dataProvider simpleIdentifiersProvider
	 * 
	 * @param string $identifier
	 */
	public function testSimpleIdentifiers($identifier) {
		$v = new VariableIdentifier($identifier);
		
		$this->assertEquals($identifier, $v->getIdentifier());
		$this->assertEquals($identifier, $v->getVariableName());
		$this->assertFalse($v->hasPrefix());
		$this->assertFalse($v->hasSequenceNumber());
	}
	
	/**
	 * @dataProvider prefixedIdentifiersProvider
	 * 
	 * @param string $identifier
	 * @param string $expectedPrefix
	 * @param string $expectedVariableName
	 */
	public function testPrefixedIdentifiers($identifier, $expectedPrefix, $expectedVariableName) {
		$v = new VariableIdentifier($identifier);
		
		$this->assertEquals($identifier, $v->getIdentifier());
		$this->assertTrue($v->hasPrefix());
		$this->assertFalse($v->hasSequenceNumber());
		$this->assertEquals($expectedPrefix, $v->getPrefix());
		$this->assertEquals($expectedVariableName, $v->getVariableName());
	}
	
	/**
	 * @dataProvider sequencedIdentifiersProvider
	 * 
	 * @param string $identifier
	 * @param string $expectedPrefix
	 * @param string $expectedSequence
	 * @param string $expectedVariableName
	 */
	public function testSequencedIdentifiers($identifier, $expectedPrefix, $expectedSequence, $expectedVariableName) {
		$v = new VariableIdentifier($identifier);
		
		$this->assertEquals($identifier, $v->getIdentifier());
		$this->assertTrue($v->hasPrefix());
		$this->assertTrue($v->hasSequenceNumber());
		$this->assertEquals($expectedPrefix, $v->getPrefix());
		$this->assertEquals($expectedVariableName, $v->getVariableName());
		$this->assertEquals($expectedSequence, $v->getSequenceNumber());
	}
	
	public function invalidIdentifierProvider() {
		return array(
			array('Q*01'),
			array('_Q01'),
			array(''),
			array(1337),
			array('Q01.A.SCORE'),
			array('Qxx.12.'),
			array('Q-2.'),
			array('934.9.SCORE'),
			array('Q01.1.SCORE.MAX'),
			array('Q 01'),
			array('Q01 . SCORE'),
			array('Q01._SCORE')
		);
	}
	
	public function simpleIdentifiersProvider() {
		return array(
			array('Q01'),
			array('Q_01'),
			array('MAXSCORE3')		
		);
	}
	
	public function prefixedIdentifiersProvider() {
		return array(
			array('Q01.SCORE', 'Q01', 'SCORE'),
			array('Q_01.SCORE', 'Q_01', 'SCORE'),
			array('Question.MAX', 'Question', 'MAX')	
		);
	}
	
	public function sequencedIdentifiersProvider() {
		return array(
			array('Q01.1.SCORE', 'Q01', 1, 'SCORE')	,
			array('Q_01.245.MAX', 'Q_01', 245, 'MAX')
		);
	}
}