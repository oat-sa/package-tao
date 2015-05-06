<?php

use qtism\data\ExtendedAssessmentItemRef;
use qtism\data\storage\xml\marshalling\CompactMarshallerFactory;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class CompactMarshallerFactyoryTest extends QtiSmTestCase {

	public function testInstantiation() {
		$factory = new CompactMarshallerFactory();
		$this->assertInstanceOf('qtism\\data\\storage\\xml\\marshalling\\CompactMarshallerFactory', $factory);
		
		$this->assertTrue($factory->hasMappingEntry('assessmentItemRef'));
		$this->assertEquals('qtism\\data\\storage\\xml\\marshalling\\ExtendedAssessmentItemRefMarshaller', $factory->getMappingEntry('assessmentItemRef'));
	}
	
	public function testFromDomElement() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<assessmentItemRef xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="Q01" href="./q01.xml"/>');
		$element = $dom->documentElement;
		
		$factory = new CompactMarshallerFactory();
		$marshaller = $factory->createMarshaller($element);
		$this->assertInstanceOf('qtism\\data\\storage\\xml\\marshalling\\ExtendedAssessmentItemRefMarshaller', $marshaller);
	}
	
	public function testFromComponent() {
		$component = new ExtendedAssessmentItemRef('Q01', './q01.xml');
	}
}