<?php

use qtism\data\state\OutcomeDeclaration;
use qtism\data\state\OutcomeDeclarationCollection;
use qtism\common\enums\Cardinality;
use qtism\common\enums\BaseType;
use qtism\data\state\ResponseDeclarationCollection;
use qtism\data\state\ResponseDeclaration;
use qtism\data\state\Weight;
use qtism\data\state\WeightCollection;
use qtism\data\ExtendedAssessmentItemRef;
use qtism\data\storage\xml\marshalling\CompactMarshallerFactory;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class ExtendedAssessmentItemRefMarshallerTest extends QtiSmTestCase {

	public function testMarshallMinimal() {
		$factory = new CompactMarshallerFactory();
		$component = new ExtendedAssessmentItemRef('Q01', './q01.xml');
		$marshaller = $factory->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('assessmentItemRef', $element->nodeName);
		$this->assertEquals('Q01', $element->getAttribute('identifier'));
		$this->assertEquals('./q01.xml', $element->getAttribute('href'));
	}
	
	public function testUnmarshallMinimal() {
		$factory = new CompactMarshallerFactory();
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<assessmentItemRef xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="Q01" href="./q01.xml" timeDependent="false"/>');
		$element = $dom->documentElement;
		$marshaller = $factory->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\ExtendedAssessmentItemRef', $component);
		$this->assertFalse($component->isTimeDependent());
		$this->assertFalse($component->isAdaptive());
		$this->assertEquals(0, count($component->getOutcomeDeclarations()));
		$this->assertEquals(0, count($component->getResponseDeclarations()));
		$this->assertEquals('Q01', $component->getIdentifier());
		$this->assertEquals('./q01.xml', $component->getHref());
	}
	
	public function testMarshallModerate() {
		$factory = new CompactMarshallerFactory();
		$component = new ExtendedAssessmentItemRef('Q01', './q01.xml');
		$weights = new WeightCollection(); // some noise
		$weights[] = new Weight('W01', 1.0);
		$weights[] = new Weight('W02', 2.0);
		
		$responseDeclarations = new ResponseDeclarationCollection();
		$responseDeclarations[] = new ResponseDeclaration('R01', BaseType::INTEGER, Cardinality::SINGLE);
		$responseDeclarations[] = new ResponseDeclaration('R02', BaseType::BOOLEAN, Cardinality::SINGLE);
		
		$outcomeDeclarations = new OutcomeDeclarationCollection();
		$outcomeDeclarations[] = new OutcomeDeclaration('O01', BaseType::FLOAT, Cardinality::SINGLE);
		$outcomeDeclarations[] = new OutcomeDeclaration('O02', BaseType::FLOAT, Cardinality::SINGLE);
		
		$component->setWeights($weights);
		$component->setResponseDeclarations($responseDeclarations);
		$component->setOutcomeDeclarations($outcomeDeclarations);
		
		$marshaller = $factory->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('assessmentItemRef', $element->nodeName);
		$this->assertEquals('Q01', $element->getAttribute('identifier'));
		$this->assertEquals('./q01.xml', $element->getAttribute('href'));
		
		$weightElts = $element->getElementsByTagName('weight');
		$this->assertEquals(2, $weightElts->length);
		$this->assertEquals('W01', $weightElts->item(0)->getAttribute('identifier'));
		$this->assertEquals('W02', $weightElts->item(1)->getAttribute('identifier'));
		
		$responseDeclarationElts = $element->getElementsByTagName('responseDeclaration');
		$this->assertEquals(2, $responseDeclarationElts->length);
		$this->assertEquals('R01', $responseDeclarationElts->item(0)->getAttribute('identifier'));
		$this->assertEquals('R02', $responseDeclarationElts->item(1)->getAttribute('identifier'));
		
		$outcomeDeclarationElts = $element->getElementsByTagName('outcomeDeclaration');
		$this->assertEquals(2, $outcomeDeclarationElts->length);
		$this->assertEquals('O01', $outcomeDeclarationElts->item(0)->getAttribute('identifier'));
		$this->assertEquals('O02', $outcomeDeclarationElts->item(1)->getAttribute('identifier'));
	}
	
	public function testUnmarshallModerate() {
		$factory = new CompactMarshallerFactory();
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<assessmentItemRef xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="Q01" href="./q01.xml" timeDependent="true" adaptive="true">
				<weight identifier="W01" value="1.0"/>
				<weight identifier="W02" value="2.0"/>
				<responseDeclaration identifier="R01" baseType="integer" cardinality="single"/>
				<responseDeclaration identifier="R02" baseType="boolean" cardinality="single"/>
				<outcomeDeclaration identifier="O01" baseType="float" cardinality="single"/>
				<outcomeDeclaration identifier="O02" baseType="float" cardinality="single"/>
			</assessmentItemRef>
			');
		$element = $dom->documentElement;
		$marshaller = $factory->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\ExtendedAssessmentItemRef', $component);
		$this->assertEquals('Q01', $component->getIdentifier());
		$this->assertTrue($component->isTimeDependent());
		$this->assertTrue($component->isAdaptive());
		$this->assertEquals('./q01.xml', $component->getHref());
		
		$weights = $component->getWeights();
		$this->assertEquals('W01', $weights['W01']->getIdentifier());
		$this->assertEquals('W02', $weights['W02']->getIdentifier());
		
		$responseDeclarations = $component->getResponseDeclarations();
		$this->assertEquals('R01', $responseDeclarations['R01']->getIdentifier());
		$this->assertEquals('R02', $responseDeclarations['R02']->getIdentifier());
		
		$outcomeDeclarations = $component->getOutcomeDeclarations();
		$this->assertEquals('O01', $outcomeDeclarations['O01']->getIdentifier());
		$this->assertEquals('O02', $outcomeDeclarations['O02']->getIdentifier());
		
	}
}