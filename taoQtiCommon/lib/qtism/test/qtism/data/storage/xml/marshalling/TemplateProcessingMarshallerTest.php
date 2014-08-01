<?php

use qtism\data\rules\TemplateCondition;
use qtism\data\processing\TemplateProcessing;
use qtism\data\rules\ExitTemplate;
use qtism\data\rules\SetCorrectResponse;
use qtism\data\rules\TemplateRuleCollection;
use qtism\data\rules\TemplateIf;
use qtism\common\enums\BaseType;
use qtism\data\expressions\BaseValue;
use qtism\data\rules\TemplateConstraint;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class TemplateProcessingMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    
	    $templateConstraint = new TemplateConstraint(new BaseValue(BaseType::BOOLEAN, true));
	    $templateIf = new TemplateIf(new BaseValue(BaseType::BOOLEAN, true), new TemplateRuleCollection(array(new SetCorrectResponse('RESPONSE', new BaseValue(BaseType::IDENTIFIER, 'jerome')))));
	    $templateCondition = new TemplateCondition($templateIf);
	    $exitTemplate = new ExitTemplate();
	    $templateProcessing = new TemplateProcessing(new TemplateRuleCollection(array($templateConstraint, $templateCondition, $exitTemplate)));
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($templateProcessing)->marshall($templateProcessing);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<templateProcessing><templateConstraint><baseValue baseType="boolean">true</baseValue></templateConstraint><templateCondition><templateIf><baseValue baseType="boolean">true</baseValue><setCorrectResponse identifier="RESPONSE"><baseValue baseType="identifier">jerome</baseValue></setCorrectResponse></templateIf></templateCondition><exitTemplate/></templateProcessing>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('
	        <templateProcessing>
	            <templateConstraint>
	                <baseValue baseType="boolean">true</baseValue>
	            </templateConstraint>
	            <templateCondition>
                    <templateIf>
	                    <baseValue baseType="boolean">true</baseValue>
	                    <setCorrectResponse identifier="RESPONSE">
	                        <baseValue baseType="identifier">jerome</baseValue>    
	                    </setCorrectResponse>    
	                </templateIf>
	            </templateCondition>
	            <exitTemplate/>
	        </templateProcessing>
	    ');
	    
	    $templateProcessing = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\processing\\TemplateProcessing', $templateProcessing);
	    $templateRules = $templateProcessing->getTemplateRules();
	    $this->assertEquals(3, count($templateRules));
	    
	    $templateConstraint = $templateRules[0];
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateConstraint', $templateConstraint);
	    
	    $templateCondition = $templateRules[1];
	    $this->assertInstanceOf('qtism\\data\\rules\\TemplateCondition', $templateCondition);
	    
	    $exitTemplate = $templateRules[2];
	    $this->assertInstanceOf('qtism\\data\\rules\\ExitTemplate', $exitTemplate);
	}
}