<?php

use qtism\data\rules\SetTemplateValue;
use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\operators\Match;
use qtism\common\enums\BaseType;
use qtism\data\expressions\BaseValue;
use qtism\data\expressions\Variable;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class SetTemplateValueMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    $variableExpr = new Variable('var1');
	    $boolExpr = new BaseValue(BaseType::BOOLEAN, true);
	    $matchExpr = new Match(new ExpressionCollection(array($variableExpr, $boolExpr)));
	    
	    $setTemplateValue = new SetTemplateValue('tpl1', $matchExpr);
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($setTemplateValue)->marshall($setTemplateValue);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<setTemplateValue identifier="tpl1"><match><variable identifier="var1"/><baseValue baseType="boolean">true</baseValue></match></setTemplateValue>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('
	        <setTemplateValue identifier="tpl1">
	            <match>
	                <variable identifier="var1"/>
	                <baseValue baseType="boolean">true</baseValue>
	            </match>
	        </setTemplateValue>
	    ');
	    
	    $setTemplateValue = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\rules\\SetTemplateValue', $setTemplateValue);
	    $this->assertEquals('tpl1', $setTemplateValue->getIdentifier());
	    $this->assertInstanceOf('qtism\\data\\expressions\\operators\\Match', $setTemplateValue->getExpression());
	}
}