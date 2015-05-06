<?php

use qtism\data\rules\TemplateElse;
use qtism\data\rules\TemplateElseIf;
use qtism\data\rules\TemplateRuleCollection;
use qtism\data\rules\SetTemplateValue;
use qtism\data\rules\TemplateIf;
use qtism\common\enums\BaseType;
use qtism\data\expressions\BaseValue;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class TemplateControlMarshallerTest extends QtiSmTestCase {

	public function testMarshallTemplateIfSimple() {
	    $true = new BaseValue(BaseType::BOOLEAN, true);
	    $setTemplateValue = new SetTemplateValue('tpl1', new BaseValue(BaseType::INTEGER, 1337));
	    $templateIf = new TemplateIf($true, new TemplateRuleCollection(array($setTemplateValue)));
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($templateIf)->marshall($templateIf);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<templateIf><baseValue baseType="boolean">true</baseValue><setTemplateValue identifier="tpl1"><baseValue baseType="integer">1337</baseValue></setTemplateValue></templateIf>', $dom->saveXML($element));
	}
	
	public function testUnmarshallTemplateIfSimple() {
	    $element = $this->createDOMElement('
	        <templateIf>
	            <baseValue baseType="boolean">true</baseValue>
	            <setTemplateValue identifier="tpl1">
	                <baseValue baseType="integer">1337</baseValue>
	            </setTemplateValue>
	        </templateIf>
	    ');
	    
	    $templateIf = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateIf', $templateIf);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateIf->getExpression());
	    $templateRules = $templateIf->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\SetTemplateValue', $templateRules[0]);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateRules[0]->getExpression());
	}
	
	public function testMarshallTemplateIfMultipleRules() {
	    $true = new BaseValue(BaseType::BOOLEAN, true);
	    $setTemplateValue1 = new SetTemplateValue('tpl1', new BaseValue(BaseType::INTEGER, 1337));
	    $setTemplateValue2 = new SetTemplateValue('tpl2', new BaseValue(BaseType::INTEGER, 1338));
	    $templateIf = new TemplateIf($true, new TemplateRuleCollection(array($setTemplateValue1, $setTemplateValue2)));
	     
	    $element = $this->getMarshallerFactory()->createMarshaller($templateIf)->marshall($templateIf);
	     
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<templateIf><baseValue baseType="boolean">true</baseValue><setTemplateValue identifier="tpl1"><baseValue baseType="integer">1337</baseValue></setTemplateValue><setTemplateValue identifier="tpl2"><baseValue baseType="integer">1338</baseValue></setTemplateValue></templateIf>', $dom->saveXML($element));
	}
	
	public function testUnmarshallTemplateIfMultipleRules() {
	    $element = $this->createDOMElement('
	        <templateIf>
	            <baseValue baseType="boolean">true</baseValue>
	            <setTemplateValue identifier="tpl1">
	                <baseValue baseType="integer">1337</baseValue>
	            </setTemplateValue>
	            <setTemplateValue identifier="tpl2">
	                <baseValue baseType="integer">1338</baseValue>
	            </setTemplateValue>
	        </templateIf>
	    ');
	     
	    $templateIf = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateIf', $templateIf);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateIf->getExpression());
	    
	    $templateRules = $templateIf->getTemplateRules();
	    $this->assertEquals(2, count($templateRules));
	    
	    $this->assertInstanceOf('qtism\\data\\rules\\SetTemplateValue', $templateRules[0]);
	    $this->assertEquals('tpl1', $templateRules[0]->getIdentifier());
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateRules[0]->getExpression());
	    $this->assertEquals(1337, $templateRules[0]->getExpression()->getValue());
	    
	    $this->assertInstanceOf('qtism\\data\\rules\\SetTemplateValue', $templateRules[1]);
	    $this->assertEquals('tpl2', $templateRules[1]->getIdentifier());
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateRules[1]->getExpression());
	    $this->assertEquals(1338, $templateRules[1]->getExpression()->getValue());
	}
	
	public function testMarshallTemplateElseIfSimple() {
	    $true = new BaseValue(BaseType::BOOLEAN, true);
	    $setTemplateValue = new SetTemplateValue('tpl1', new BaseValue(BaseType::INTEGER, 1337));
	    $templateElseIf = new TemplateElseIf($true, new TemplateRuleCollection(array($setTemplateValue)));
	     
	    $element = $this->getMarshallerFactory()->createMarshaller($templateElseIf)->marshall($templateElseIf);
	     
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<templateElseIf><baseValue baseType="boolean">true</baseValue><setTemplateValue identifier="tpl1"><baseValue baseType="integer">1337</baseValue></setTemplateValue></templateElseIf>', $dom->saveXML($element));
	}
	
	public function testUnmarshallTemplateElseIfSimple() {
	    $element = $this->createDOMElement('
	        <templateElseIf>
	            <baseValue baseType="boolean">true</baseValue>
	            <setTemplateValue identifier="tpl1">
	                <baseValue baseType="integer">1337</baseValue>
	            </setTemplateValue>
	        </templateElseIf>
	    ');
	     
	    $templateElseIf = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateElseIf', $templateElseIf);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateElseIf->getExpression());
	    $templateRules = $templateElseIf->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\SetTemplateValue', $templateRules[0]);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateRules[0]->getExpression());
	}
	
	public function testMarshallTemplateElseSimple() {
	    $setTemplateValue = new SetTemplateValue('tpl1', new BaseValue(BaseType::INTEGER, 1337));
	    $templateIf = new TemplateElse(new TemplateRuleCollection(array($setTemplateValue)));
	     
	    $element = $this->getMarshallerFactory()->createMarshaller($templateIf)->marshall($templateIf);
	     
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<templateElse><setTemplateValue identifier="tpl1"><baseValue baseType="integer">1337</baseValue></setTemplateValue></templateElse>', $dom->saveXML($element));
	}
	
	public function testUnmarshallTemplateElseSimple() {
	    $element = $this->createDOMElement('
	        <templateElse>
	            <setTemplateValue identifier="tpl1">
	                <baseValue baseType="integer">1337</baseValue>
	            </setTemplateValue>
	        </templateElse>
	    ');
	     
	    $templateElse = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateElse', $templateElse);
	    $templateRules = $templateElse->getTemplateRules();
	    $this->assertEquals(1, count($templateRules));
	    $this->assertInstanceOf('qtism\\data\\rules\\SetTemplateValue', $templateRules[0]);
	    $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $templateRules[0]->getExpression());
	}
	
	public function testMarshallTemplateElseMultipleRules() {
	    $setTemplateValue1 = new SetTemplateValue('tpl1', new BaseValue(BaseType::INTEGER, 1337));
	    $setTemplateValue2 = new SetTemplateValue('tpl2', new BaseValue(BaseType::INTEGER, 1338));
	    $templateElse = new TemplateElse(new TemplateRuleCollection(array($setTemplateValue1, $setTemplateValue2)));
	
	    $element = $this->getMarshallerFactory()->createMarshaller($templateElse)->marshall($templateElse);
	
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<templateElse><setTemplateValue identifier="tpl1"><baseValue baseType="integer">1337</baseValue></setTemplateValue><setTemplateValue identifier="tpl2"><baseValue baseType="integer">1338</baseValue></setTemplateValue></templateElse>', $dom->saveXML($element));
	}
}