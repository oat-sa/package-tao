<?php

use qtism\data\expressions\operators\ToleranceMode;
use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\operators\Equal;
use qtism\common\enums\BaseType;
use qtism\data\expressions\BaseValue;
use qtism\data\expressions\operators\CustomOperator;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class CustomOperatorMarshallerTest extends QtiSmTestCase {

	public function testMarshallNoLaxContent() {
	    
	    $int1 = new BaseValue(BaseType::INTEGER, 1);
	    $int2 = new BaseValue(BaseType::INTEGER, 1);
	    $equal = new Equal(new ExpressionCollection(array($int1, $int2)));
	    
	    $customOperator = new CustomOperator(new ExpressionCollection(array($equal)), '<customOperator><equal toleranceMode="exact"><baseValue baseType="integer">1</baseValue><baseValue baseType="integer">1</baseValue></equal></customOperator>');
		$element = $this->getMarshallerFactory()->createMarshaller($customOperator)->marshall($customOperator);
		$dom = new DOMDocument('1.0', 'UTF-8');
		$element = $dom->importNode($element, true);
		$this->assertEquals('<customOperator><equal toleranceMode="exact"><baseValue baseType="integer">1</baseValue><baseValue baseType="integer">1</baseValue></equal></customOperator>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
 		$element = $this->createDOMElement('<customOperator><equal toleranceMode="exact"><baseValue baseType="integer">1</baseValue><baseValue baseType="integer">1</baseValue></equal></customOperator>');
		
 		$component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
 		$this->assertInstanceOf('qtism\\data\\expressions\\operators\\CustomOperator', $component);
 		
 		$expressions = $component->getExpressions();
 		$this->assertEquals(1, count($expressions));
 		$this->assertInstanceOf('qtism\\data\\expressions\\operators\\Equal', $expressions[0]);
 		$this->assertEquals(ToleranceMode::EXACT, $expressions[0]->getToleranceMode());
 		
 		$subExpressions = $expressions[0]->getExpressions();
 		$this->assertEquals(2, count($subExpressions));
 		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $subExpressions[0]);
 		$this->assertEquals(BaseType::INTEGER, $subExpressions[0]->getBaseType());
 		$this->assertEquals(1, $subExpressions[0]->getValue());
 		$this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $subExpressions[1]);
 		$this->assertEquals(BaseType::INTEGER, $subExpressions[1]->getBaseType());
 		$this->assertEquals(1, $subExpressions[1]->getValue());
	}
}
