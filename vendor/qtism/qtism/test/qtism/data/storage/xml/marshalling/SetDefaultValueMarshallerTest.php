<?php

use qtism\data\rules\SetDefaultValue;
use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\operators\Match;
use qtism\common\enums\BaseType;
use qtism\data\expressions\BaseValue;
use qtism\data\expressions\Variable;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class SetDefaultValueMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    $variableExpr = new Variable('var1');
	    $boolExpr = new BaseValue(BaseType::BOOLEAN, true);
	    $matchExpr = new Match(new ExpressionCollection(array($variableExpr, $boolExpr)));
	    
	    $setDefaultValue = new SetDefaultValue('tpl1', $matchExpr);
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($setDefaultValue)->marshall($setDefaultValue);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<setDefaultValue identifier="tpl1"><match><variable identifier="var1"/><baseValue baseType="boolean">true</baseValue></match></setDefaultValue>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('
	        <setDefaultValue identifier="tpl1">
	            <match>
	                <variable identifier="var1"/>
	                <baseValue baseType="boolean">true</baseValue>
	            </match>
	        </setDefaultValue>
	    ');
	    
	    $setDefaultValue = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\rules\\SetDefaultValue', $setDefaultValue);
	    $this->assertEquals('tpl1', $setDefaultValue->getIdentifier());
	    $this->assertInstanceOf('qtism\\data\\expressions\\operators\\Match', $setDefaultValue->getExpression());
	}
}