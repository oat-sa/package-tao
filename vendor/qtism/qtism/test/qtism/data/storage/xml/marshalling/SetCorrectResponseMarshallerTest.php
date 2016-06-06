<?php

use qtism\data\rules\SetCorrectResponse;
use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\operators\Match;
use qtism\common\enums\BaseType;
use qtism\data\expressions\BaseValue;
use qtism\data\expressions\Variable;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class SetCorrectResponseMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    $variableExpr = new Variable('var1');
	    $boolExpr = new BaseValue(BaseType::BOOLEAN, true);
	    $matchExpr = new Match(new ExpressionCollection(array($variableExpr, $boolExpr)));
	    
	    $setCorrectResponse = new SetCorrectResponse('tpl1', $matchExpr);
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($setCorrectResponse)->marshall($setCorrectResponse);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<setCorrectResponse identifier="tpl1"><match><variable identifier="var1"/><baseValue baseType="boolean">true</baseValue></match></setCorrectResponse>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('
	        <setCorrectResponse identifier="tpl1">
	            <match>
	                <variable identifier="var1"/>
	                <baseValue baseType="boolean">true</baseValue>
	            </match>
	        </setCorrectResponse>
	    ');
	    
	    $setCorrectResponse = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\rules\\SetCorrectResponse', $setCorrectResponse);
	    $this->assertEquals('tpl1', $setCorrectResponse->getIdentifier());
	    $this->assertInstanceOf('qtism\\data\\expressions\\operators\\Match', $setCorrectResponse->getExpression());
	}
}