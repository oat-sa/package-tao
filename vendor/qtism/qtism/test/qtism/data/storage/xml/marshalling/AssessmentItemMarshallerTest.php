<?php

use qtism\data\state\ResponseDeclarationCollection;
use qtism\data\state\OutcomeDeclarationCollection;
use qtism\data\state\ResponseDeclaration;
use qtism\data\state\OutcomeDeclaration;
use qtism\common\enums\Cardinality;
use qtism\common\enums\BaseType;
use qtism\data\AssessmentItem;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class AssessmentItemMarshallerTest extends QtiSmTestCase {

	public function testMarshallMinimal() {

		$identifier = 'Q01';
		$timeDependent = false;
	    $title = 'Question 1';
	    $label = 'Label of Question 1';
	    $toolName = 'QTISM';
	    $toolVersion = '0.6.0';
	    
		$assessmentItem = new AssessmentItem($identifier, $title, $timeDependent);
		$assessmentItem->setLabel($label);
		$assessmentItem->setToolName($toolName);
		$assessmentItem->setToolVersion($toolVersion);
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($assessmentItem);
		$element = $marshaller->marshall($assessmentItem);
		
		$this->assertInstanceOf('\DOMElement', $element);
		$this->assertEquals('assessmentItem', $element->nodeName);
		
		// adaptive, timeDependent, identifier, title, label, toolName, toolVersion
		$this->assertEquals($element->attributes->length, 7);
		$this->assertEquals($identifier, $element->getAttribute('identifier'));
		$this->assertEquals($title, $element->getAttribute('title'));
		$this->assertEquals('false', $element->getAttribute('timeDependent'));
		$this->assertEquals('false', $element->getAttribute('adaptive'));
		$this->assertEquals($label, $element->getAttribute('label'));
		$this->assertEquals($toolName, $element->getAttribute('toolName'));
		$this->assertEquals($toolVersion, $element->getAttribute('toolVersion'));
	}
	
	public function testUnmarshallMinimal() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="Q01" title="Test Item" timeDependent="false" label="My Label" toolName="My Tool" toolVersion="0.6.0"/>
			');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\assessmentItem', $component);
		$this->assertEquals('Q01', $component->getIdentifier());
		$this->assertEquals('Test Item', $component->getTitle());
		$this->assertEquals(false, $component->isTimeDependent());
		$this->assertEquals(false, $component->isAdaptive());
		$this->assertFalse($component->hasLang());
		$this->assertTrue($component->hasLabel());
		$this->assertEquals('My Label', $component->getLabel());
		$this->assertTrue($component->hasToolName());
		$this->assertEquals('My Tool', $component->getToolName());
		$this->assertTrue($component->hasToolVersion());
		$this->assertEquals('0.6.0', $component->getToolVersion());
	}
	
	public function testMarshallMaximal() {
		$identifier = 'Q01';
		$title = 'Test Item';
		$timeDependent = true;
		$adaptive = true;
		$lang = 'en-YO'; // Yoda English ;)
		
		$responseDeclarations = new ResponseDeclarationCollection();
		$responseDeclarations[] = new ResponseDeclaration('resp1', BaseType::INTEGER, Cardinality::SINGLE);
		$responseDeclarations[] = new ResponseDeclaration('resp2', BaseType::FLOAT, Cardinality::SINGLE);
		
		$outcomeDeclarations = new OutcomeDeclarationCollection();
		$outcomeDeclarations[] = new OutcomeDeclaration('out1', BaseType::BOOLEAN, Cardinality::MULTIPLE);
		$outcomeDeclarations[] = new OutcomeDeclaration('out2', BaseType::IDENTIFIER, Cardinality::SINGLE);
		
		$item = new AssessmentItem($identifier, $title, $timeDependent, $lang);
		$item->setAdaptive($adaptive);
		$item->setResponseDeclarations($responseDeclarations);
		$item->setOutcomeDeclarations($outcomeDeclarations);
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($item);
		$element = $marshaller->marshall($item);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('assessmentItem', $element->nodeName);
		
		// adaptive, timeDependent, identifier, lang, title
		$this->assertEquals($element->attributes->length, 5);
		$this->assertEquals($identifier, $element->getAttribute('identifier'));
		$this->assertEquals($title, $element->getAttribute('title'));
		$this->assertEquals('true', $element->getAttribute('timeDependent'));
		$this->assertEquals('true', $element->getAttribute('adaptive'));
		$this->assertEquals($lang, $element->getAttribute('lang'));
		
		$responseDeclarationElts = $element->getElementsByTagName('responseDeclaration');
		$this->assertEquals(2, $responseDeclarationElts->length);
		$this->assertEquals('resp1', $responseDeclarationElts->item(0)->getAttribute('identifier'));
		$this->assertEquals('resp2', $responseDeclarationElts->item(1)->getAttribute('identifier'));
		
		$outcomeDeclarationElts = $element->getElementsByTagName('outcomeDeclaration');
		$this->assertEquals(2, $outcomeDeclarationElts->length);
		$this->assertEquals('out1', $outcomeDeclarationElts->item(0)->getAttribute('identifier'));
		$this->assertEquals('out2', $outcomeDeclarationElts->item(1)->getAttribute('identifier'));
	}
	
	public function testUnmarshallMaximal() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="Q01" timeDependent="false" adaptive="false" lang="en-YO" title="test item">
				<responseDeclaration identifier="resp1" baseType="integer" cardinality="single"/>
				<responseDeclaration identifier="resp2" baseType="float" cardinality="single"/>
				<outcomeDeclaration identifier="out1" baseType="boolean" cardinality="multiple"/>
				<outcomeDeclaration identifier="out2" baseType="identifier" cardinality="single"/>
			</assessmentItem>
			');
		$element = $dom->documentElement;
	
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
	
		$this->assertInstanceOf('qtism\\data\\assessmentItem', $component);
		$this->assertEquals('Q01', $component->getIdentifier());
		$this->assertEquals('test item', $component->getTitle());
		$this->assertEquals(false, $component->isTimeDependent());
		$this->assertEquals(false, $component->isAdaptive());
		$this->assertTrue($component->hasLang());
		$this->assertEquals('en-YO', $component->getLang());
		
		$responseDeclarations = $component->getResponseDeclarations();
		$this->assertEquals(2, count($responseDeclarations));
		
		$outcomeDeclarations = $component->getOutcomeDeclarations();
		$this->assertEquals(2, count($outcomeDeclarations));
	}
}
