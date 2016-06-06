<?php

use qtism\data\rules\OutcomeIf;
use qtism\data\rules\OutcomeElseIf;
use qtism\data\rules\OutcomeElse;
use qtism\common\enums\BaseType;
use qtism\data\expressions\BaseValue;
use qtism\data\rules\SetOutcomeValue;
use qtism\data\rules\OutcomeRuleCollection;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class OutcomeControlMarshallerTest extends QtiSmTestCase {

	public function testMarshallIfMinimal() {

		$setOutcomeValue = new SetOutcomeValue('myStringVar', new BaseValue(BaseType::STRING, 'Tested!'));
		$baseValue = new BaseValue(BaseType::BOOLEAN, true);
		
		$component = new OutcomeIf($baseValue, new OutcomeRuleCollection(array($setOutcomeValue)));
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('outcomeIf', $element->nodeName);
		$this->assertEquals(2, $element->getElementsByTagName('baseValue')->length);
		
		$expression = $element->getElementsByTagName('baseValue')->item(0);
		$this->assertTrue($element === $expression->parentNode);
		$this->assertEquals('boolean', $expression->getAttribute('baseType'));
		$this->assertEquals('true', $expression->nodeValue);
		
		$setOutcomeValue = $element->getElementsByTagName('setOutcomeValue')->item(0);
		$this->assertEquals('myStringVar', $setOutcomeValue->getAttribute('identifier'));
		
		$tested = $element->getElementsByTagName('baseValue')->item(1);
		$this->assertTrue($setOutcomeValue === $tested->parentNode);
		$this->assertEquals('Tested!', $tested->nodeValue);
		$this->assertEquals('string', $tested->getAttribute('baseType'));
	}
	
	public function testMarshallElseIfMinimal() {
		
		$setOutcomeValue = new SetOutcomeValue('myStringVar', new BaseValue(BaseType::STRING, 'Tested!'));
		$baseValue = new BaseValue(BaseType::BOOLEAN, true);
	
		$component = new OutcomeElseIf($baseValue, new OutcomeRuleCollection(array($setOutcomeValue)));
	
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
	
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('outcomeElseIf', $element->nodeName);
		$this->assertEquals(2, $element->getElementsByTagName('baseValue')->length);
	
		$expression = $element->getElementsByTagName('baseValue')->item(0);
		$this->assertTrue($element === $expression->parentNode);
		$this->assertEquals('boolean', $expression->getAttribute('baseType'));
		$this->assertEquals('true', $expression->nodeValue);
	
		$setOutcomeValue = $element->getElementsByTagName('setOutcomeValue')->item(0);
		$this->assertEquals('myStringVar', $setOutcomeValue->getAttribute('identifier'));
	
		$tested = $element->getElementsByTagName('baseValue')->item(1);
		$this->assertTrue($setOutcomeValue === $tested->parentNode);
		$this->assertEquals('Tested!', $tested->nodeValue);
		$this->assertEquals('string', $tested->getAttribute('baseType'));
	}
	
	public function testMarshallElseMinimal() {

		$setOutcomeValue = new SetOutcomeValue('myStringVar', new BaseValue(BaseType::STRING, 'Tested!'));
		$component = new OutcomeElse(new OutcomeRuleCollection(array($setOutcomeValue)));
	
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
	
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('outcomeElse', $element->nodeName);
		$this->assertEquals(1, $element->getElementsByTagName('baseValue')->length);
	
		$setOutcomeValue = $element->getElementsByTagName('setOutcomeValue')->item(0);
		$this->assertEquals('myStringVar', $setOutcomeValue->getAttribute('identifier'));
		
		$tested = $element->getElementsByTagName('baseValue')->item(0);
		$this->assertTrue($setOutcomeValue === $tested->parentNode);
		$this->assertEquals('string', $tested->getAttribute('baseType'));
		$this->assertEquals('Tested!', $tested->nodeValue);
	}
	
	public function testUnmarshallIfMinimal() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<outcomeIf xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1">
				<baseValue baseType="boolean">true</baseValue>
				<setOutcomeValue identifier="myStringVar">
					<baseValue baseType="string">Tested!</baseValue>
				</setOutcomeValue>
			</outcomeIf>
			'
		);
		$element = $dom->documentElement;

		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\rules\\OutcomeIf', $component);
		$this->assertEquals(1, count($component->getOutcomeRules()));
		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $component->getExpression());
		
		$outcomeRules = $component->getOutcomeRules();
		$this->assertInstanceOf('qtism\\data\\rules\\SetOutcomeValue', $outcomeRules[0]);
		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $outcomeRules[0]->getExpression());
		$this->assertInternalType('string', $outcomeRules[0]->getExpression()->getValue());
		$this->assertEquals('Tested!', $outcomeRules[0]->getExpression()->getValue());
		$this->assertEquals(BaseType::STRING, $outcomeRules[0]->getExpression()->getBaseType());
	}
	
	public function testUnmarshallElseIfMinimal() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<outcomeElseIf xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1">
				<baseValue baseType="boolean">true</baseValue>
				<setOutcomeValue identifier="myStringVar">
					<baseValue baseType="string">Tested!</baseValue>
				</setOutcomeValue>
			</outcomeElseIf>
			'
		);
		$element = $dom->documentElement;
	
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
	
		$this->assertInstanceOf('qtism\\data\\rules\\OutcomeElseIf', $component);
		$this->assertEquals(1, count($component->getOutcomeRules()));
		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $component->getExpression());
		
		$outcomeRules = $component->getOutcomeRules();
		$this->assertInstanceOf('qtism\\data\\rules\\SetOutcomeValue', $outcomeRules[0]);
		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $outcomeRules[0]->getExpression());
		$this->assertInternalType('string', $outcomeRules[0]->getExpression()->getValue());
		$this->assertEquals('Tested!', $outcomeRules[0]->getExpression()->getValue());
		$this->assertEquals(BaseType::STRING, $outcomeRules[0]->getExpression()->getBaseType());
	}
	
	public function testUnmarshallElseMinimal() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
				'
			<outcomeElse xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1">
				<setOutcomeValue identifier="myStringVar">
					<baseValue baseType="string">Tested!</baseValue>
				</setOutcomeValue>
			</outcomeElse>
			'
		);
		$element = $dom->documentElement;
	
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
	
		$this->assertInstanceOf('qtism\\data\\rules\\OutcomeElse', $component);
		$this->assertEquals(1, count($component->getOutcomeRules()));
		
		$outcomeRules = $component->getOutcomeRules();
		$this->assertInstanceOf('qtism\\data\\rules\\SetOutcomeValue', $outcomeRules[0]);
		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $outcomeRules[0]->getExpression());
		$this->assertInternalType('string', $outcomeRules[0]->getExpression()->getValue());
		$this->assertEquals('Tested!', $outcomeRules[0]->getExpression()->getValue());
		$this->assertEquals(BaseType::STRING, $outcomeRules[0]->getExpression()->getBaseType());
	}
}
