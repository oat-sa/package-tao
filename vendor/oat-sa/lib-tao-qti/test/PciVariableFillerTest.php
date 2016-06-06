<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */


use qtism\runtime\common\Variable;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\common\RecordContainer;
use qtism\data\IAssessmentItem;
use qtism\data\AssessmentItem;
use qtism\data\state\OutcomeDeclarationCollection;
use qtism\data\state\OutcomeDeclaration;
use qtism\data\state\ResponseDeclarationCollection;
use qtism\data\state\ResponseDeclaration;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\Boolean;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Point;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\DirectedPair;
use qtism\common\datatypes\Duration;
use qtism\common\datatypes\Uri;
use qtism\common\datatypes\IntOrIdentifier;

/**
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @package taoQtiCommon
 
 */
class PciVariableFillerTest extends PHPUnit_Framework_TestCase {
	
    /**
     * @dataProvider fillVariableProvider
     * 
     * @param IAssessmentItem $itemRef The reference item.
     * @param string $variableIdentifier The identifier of the variable.
     * @param array $data Client-side data.
     * @param Variable $expectedVariable
     */
    public function testFillVariable(array $data, Variable $expectedVariable) {
        // Non-time dependent basic item in 'Yoda English'.
        $itemRef = new AssessmentItem('Q01', 'Question 01', false, 'en-YO');
        
        $outcomeDeclarations = new OutcomeDeclarationCollection();
        $outcomeDeclarations[] = new OutcomeDeclaration('OUTCOME1', BaseType::FLOAT, Cardinality::SINGLE);
        $outcomeDeclarations[] = new OutcomeDeclaration('OUTCOME2', BaseType::INTEGER, Cardinality::SINGLE);
        $outcomeDeclarations[] = new OutcomeDeclaration('OUTCOME3', BaseType::BOOLEAN, Cardinality::SINGLE);
        $outcomeDeclarations[] = new OutcomeDeclaration('OUTCOME4', BaseType::STRING, Cardinality::SINGLE);
        $outcomeDeclarations[] = new OutcomeDeclaration('OUTCOME5', BaseType::POINT, Cardinality::SINGLE);
        $outcomeDeclarations[] = new OutcomeDeclaration('OUTCOME6', BaseType::PAIR, Cardinality::SINGLE);
        $outcomeDeclarations[] = new OutcomeDeclaration('OUTCOME7', BaseType::DIRECTED_PAIR, Cardinality::SINGLE);
        $outcomeDeclarations[] = new OutcomeDeclaration('OUTCOME8', BaseType::DURATION, Cardinality::SINGLE);
        $outcomeDeclarations[] = new OutcomeDeclaration('OUTCOME9', BaseType::URI, Cardinality::SINGLE);
        $outcomeDeclarations[] = new OutcomeDeclaration('OUTCOME10', BaseType::IDENTIFIER, Cardinality::SINGLE);
        $outcomeDeclarations[] = new OutcomeDeclaration('OUTCOME11', BaseType::INT_OR_IDENTIFIER, Cardinality::SINGLE);
        $outcomeDeclarations[] = new OutcomeDeclaration('OUTCOME12', BaseType::INTEGER, Cardinality::SINGLE);
        
        $responseDeclarations = new ResponseDeclarationCollection();
        $responseDeclarations[] = new ResponseDeclaration('RESPONSE1', BaseType::IDENTIFIER, Cardinality::SINGLE);
        $responseDeclarations[] = new ResponseDeclaration('RESPONSE2', BaseType::IDENTIFIER, Cardinality::MULTIPLE);
        $responseDeclarations[] = new ResponseDeclaration('RESPONSE3', BaseType::DIRECTED_PAIR, Cardinality::ORDERED);
        $responseDeclarations[] = new ResponseDeclaration('RESPONSE4', -1, Cardinality::RECORD);
        $responseDeclarations[] = new ResponseDeclaration('RESPONSE5', -1, Cardinality::RECORD);
        $responseDeclarations[] = new ResponseDeclaration('RESPONSE6', -1, Cardinality::RECORD);
        $responseDeclarations[] = new ResponseDeclaration('RESPONSE7', -1, Cardinality::RECORD);
        $responseDeclarations[] = new ResponseDeclaration('RESPONSE8', BaseType::BOOLEAN, Cardinality::MULTIPLE);
        
        $itemRef->setOutcomeDeclarations($outcomeDeclarations);
        $itemRef->setResponseDeclarations($responseDeclarations);
        
        $filler = new taoQtiCommon_helpers_PciVariableFiller($itemRef);
        $variable = $filler->fill($expectedVariable->getIdentifier(), $data);
        
        $this->assertEquals($expectedVariable->getIdentifier(), $variable->getIdentifier());
        $this->assertEquals($expectedVariable->getBaseType(), $variable->getBaseType());
        $this->assertEquals($expectedVariable->getCardinality(), $variable->getCardinality());
        $this->assertEquals(get_class($expectedVariable), get_class($variable));
        
        if ($expectedVariable->getValue() === null) {
            $this->assertSame($expectedVariable->getValue(), $variable->getValue());
        }
        else {
            $this->assertTrue($expectedVariable->getValue()->equals($variable->getValue()));
        }
    }
    
    public function fillVariableProvider() {
        
        $returnValue = array();
        
        $json = array('base' => array('float' => 13.37));
        $expectedVariable = new OutcomeVariable('OUTCOME1', Cardinality::SINGLE, BaseType::FLOAT, new Float(13.37));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('base' => array('integer' => 10));
        $expectedVariable = new OutcomeVariable('OUTCOME2', Cardinality::SINGLE, BaseType::INTEGER, new Integer(10));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('base' => array('boolean' => true));
        $expectedVariable = new OutcomeVariable('OUTCOME3', Cardinality::SINGLE, BaseType::BOOLEAN, new Boolean(true));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('base' => array('string' => 'String!'));
        $expectedVariable = new OutcomeVariable('OUTCOME4', Cardinality::SINGLE, BaseType::STRING, new String('String!'));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('base' => array('point' => array(10, 10)));
        $expectedVariable = new OutcomeVariable('OUTCOME5', Cardinality::SINGLE, BaseType::POINT, new Point(10, 10));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('base' => array('pair' => array('A', 'B')));
        $expectedVariable = new OutcomeVariable('OUTCOME6', Cardinality::SINGLE, BaseType::PAIR, new Pair('A', 'B'));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('base' => array('directedPair' => array('A', 'B')));
        $expectedVariable = new OutcomeVariable('OUTCOME7', Cardinality::SINGLE, BaseType::DIRECTED_PAIR, new DirectedPair('A', 'B'));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('base' => array('duration' => 'PT1S'));
        $expectedVariable = new OutcomeVariable('OUTCOME8', Cardinality::SINGLE, BaseType::DURATION, new Duration('PT1S'));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('base' => array('uri' => 'http://www.taotesting.com'));
        $expectedVariable = new OutcomeVariable('OUTCOME9', Cardinality::SINGLE, BaseType::URI, new Uri('http://www.taotesting.com'));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('base' => array('identifier' => 'ChoiceB'));
        $expectedVariable = new OutcomeVariable('OUTCOME10', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceB'));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('base' => array('intOrIdentifier' => 255));
        $expectedVariable = new OutcomeVariable('OUTCOME11', Cardinality::SINGLE, BaseType::INT_OR_IDENTIFIER, new IntOrIdentifier(255));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('base' => null);
        $expectedVariable = new OutcomeVariable('OUTCOME12', Cardinality::SINGLE, BaseType::INTEGER, null);
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('base' => array('identifier' => 'ChoiceA'));
        $expectedVariable = new ResponseVariable('RESPONSE1', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA'));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('list' => array('identifier' => array('ChoiceA', 'ChoiceC')));
        $expectedVariable = new ResponseVariable('RESPONSE2', Cardinality::MULTIPLE, BaseType::IDENTIFIER, new MultipleContainer(BaseType::IDENTIFIER, array(new Identifier('ChoiceA'), new Identifier('ChoiceC'))));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('list' => array('directedPair' => array(array('A', 'B'), array('C', 'D'))));
        $expectedVariable = new ResponseVariable('RESPONSE3', Cardinality::ORDERED, BaseType::DIRECTED_PAIR, new OrderedContainer(BaseType::DIRECTED_PAIR, array(new DirectedPair('A', 'B'), new DirectedPair('C', 'D'))));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('record' => array());
        $expectedVariable = new ResponseVariable('RESPONSE4', Cardinality::RECORD, -1, new RecordContainer());
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('record' => array(array('name' => 'A', 'base' => array('identifier' => 'ChoiceA')), array('name' => 'B', 'base' => array('identifier' => 'ChoiceB'))));
        $expectedVariable = new ResponseVariable('RESPONSE5', Cardinality::RECORD, -1, new RecordContainer(array('A' => new Identifier('ChoiceA'), 'B' => new Identifier('ChoiceB'))));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('record' => array(array('name' => 'A', 'base' => null)));
        $expectedVariable = new ResponseVariable('RESPONSE6', Cardinality::RECORD, -1, new RecordContainer(array('A' => null)));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('record' => array(array('name' => 'A', 'base' => array('boolean' => true)), array('name' => 'B', 'base' => null), array('name' => 'C', 'base' => array('boolean' => false))));
        $expectedVariable = new ResponseVariable('RESPONSE7', Cardinality::RECORD, -1, new RecordContainer(array('A' => new Boolean(true), 'B' => null, 'C' => new Boolean(false))));
        $returnValue[] = array($json, $expectedVariable);
        
        $json = array('list' => array('boolean' => array(true, null, false)));
        $expectedVariable = new ResponseVariable('RESPONSE8', Cardinality::MULTIPLE, BaseType::BOOLEAN, new MultipleContainer(BaseType::BOOLEAN, array(new Boolean(true), null, new Boolean(false))));
        $returnValue[] = array($json, $expectedVariable);
        
        return $returnValue;
    }
}
