<?php

use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\operators\EqualRounded;
use qtism\data\expressions\operators\RoundingMode;
use qtism\data\expressions\BaseValue;
use qtism\common\enums\BaseType;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class EqualRoundedMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$subs = new ExpressionCollection();
		$subs[] = new BaseValue(BaseType::FLOAT, 3.175);
		$subs[] = new BaseValue(BaseType::FLOAT, 3.183);
		
		$roundingMode = RoundingMode::SIGNIFICANT_FIGURES;
		$figures = 3;
		
		$component = new EqualRounded($subs, $figures, $roundingMode);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('equalRounded', $element->nodeName);
		$this->assertEquals('significantFigures', $element->getAttribute('roundingMode'));
		$this->assertEquals($figures . '', $element->getAttribute('figures'));
		$this->assertEquals(2, $element->getElementsByTagName('baseValue')->length);
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<equalRounded xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" roundingMode="significantFigures" figures="3">
				<baseValue baseType="float">3.175</baseValue>
				<baseValue baseType="float">3.183</baseValue>
			</equalRounded>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\operators\\EqualRounded', $component);
		$this->assertInternalType('integer', $component->getFigures());
		$this->assertEquals(3, $component->getFigures());
		$this->assertEquals(RoundingMode::SIGNIFICANT_FIGURES, $component->getRoundingMode());
		$this->assertEquals(2, count($component->getExpressions()));
	}
}
