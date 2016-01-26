<?php

use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\operators\Equal;
use qtism\data\expressions\operators\ToleranceMode;
use qtism\data\expressions\BaseValue;
use qtism\common\enums\BaseType;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class EqualMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		$subs = new ExpressionCollection();
		$subs[] = new BaseValue(BaseType::INTEGER, 1);
		$subs[] = new BaseValue(BaseType::INTEGER, 2);
		
		$toleranceMode = ToleranceMode::EXACT;
		$includeLowerBound = false;
		$includeUpperBound = true;
		
		$component = new Equal($subs);
		$component->setToleranceMode($toleranceMode);
		$component->setIncludeLowerBound($includeLowerBound);
		$component->setIncludeUpperBound($includeUpperBound);
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('equal', $element->nodeName);
		$this->assertEquals('exact', $element->getAttribute('toleranceMode'));
		$this->assertEquals('false', $element->getAttribute('includeLowerBound'));
		$this->assertEquals('', $element->getAttribute('includeUpperBound'));
		$this->assertEquals(2, $element->getElementsByTagName('baseValue')->length);
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<equal xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" includeLowerBound="false" includeUpperBound="true" toleranceMode="exact">
				<baseValue baseType="integer">1</baseValue>
				<baseValue baseType="integer">2</baseValue>
			</equal>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\operators\\Equal', $component);
		$this->assertInternalType('boolean', $component->doesIncludeLowerBound());
		$this->assertInternalType('boolean', $component->doesIncludeUpperBound());
		$this->assertFalse($component->doesIncludeLowerBound());
		$this->assertTrue($component->doesIncludeUpperBound());
		$this->assertEquals(2, count($component->getExpressions()));
	}
}
