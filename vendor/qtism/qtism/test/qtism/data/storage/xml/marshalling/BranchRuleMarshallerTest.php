<?php

use qtism\data\rules\BranchRule;
use qtism\data\expressions\BaseValue;
use qtism\common\enums\BaseType;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class BranchRuleMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		$target = 'target1';
		
		$component = new BranchRule(new BaseValue(BaseType::BOOLEAN, true), $target);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('branchRule', $element->nodeName);
		$this->assertEquals('baseValue', $element->getElementsByTagName('baseValue')->item(0)->nodeName);
		$this->assertEquals($target, $element->getAttribute('target'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<branchRule xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" target="target1">
				<baseValue baseType="boolean">true</baseValue>
			</branchRule>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\rules\\BranchRule', $component);
		$this->assertEquals('target1', $component->getTarget());
		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $component->getExpression());
	}
}
