<?php

use qtism\common\datatypes\Identifier;

use qtism\common\datatypes\Integer;

use qtism\data\expressions\MapResponse;

use qtism\runtime\common\RecordContainer;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\runtime\common\OutcomeVariable;
use qtism\common\enums\BaseType;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\State;
use qtism\runtime\expressions\MapResponseProcessor;
use qtism\common\datatypes\Pair;
use qtism\runtime\common\MultipleContainer;

class MapResponseProcessorTest extends QtiSmTestCase {
	
	public function testSimple() {
		$variableDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="integer" cardinality="single">
				<mapping>
					<mapEntry mapKey="0" mappedValue="1"/>
					<mapEntry mapKey="1" mappedValue="2"/>
				</mapping>
			</responseDeclaration>
		');
		$variable = ResponseVariable::createFromDataModel($variableDeclaration);
		$mapResponseExpr = $this->createComponentFromXml('<mapResponse identifier="response1"/>');
		
		$state = new State();
		$state->setVariable($variable);
		$mapResponseProcessor = new MapResponseProcessor($mapResponseExpr);
		$mapResponseProcessor->setState($state);
		
		$result = $mapResponseProcessor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		// The variable has no value so the default mapping value is returned.
		$this->assertEquals(0, $result->getValue()); 
		
		$state['response1'] = new Integer(0);
		$result = $mapResponseProcessor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(1, $result->getValue());
		
		$state['response1'] = new Integer(1);
		$result = $mapResponseProcessor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(2, $result->getValue());
		
		$state['response1'] = new Integer(240);
		$result = $mapResponseProcessor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(0, $result->getValue());
	}
	
	public function testMultipleComplexTyping() {
		$variableDeclaration = $this->createComponentFromXml('
			<responseDeclaration identifier="response1" baseType="pair" cardinality="multiple">
				<mapping defaultValue="1">
					<mapEntry mapKey="A B" mappedValue="1.5"/>
					<mapEntry mapKey="C D" mappedValue="2.5"/>
				</mapping>
			</responseDeclaration>
		');
		$variable = ResponseVariable::createFromDataModel($variableDeclaration);
		$mapResponseExpr = $this->createComponentFromXml('<mapResponse identifier="response1"/>');
		$mapResponseProcessor = new MapResponseProcessor($mapResponseExpr);
		
		$state = new State();
		$state->setVariable($variable);
		$mapResponseProcessor->setState($state);
		
		// No value could be tried to be matched.
		$result = $mapResponseProcessor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(0.0, $result->getValue());
		
		$state['response1'] = new MultipleContainer(BaseType::PAIR, array(new Pair('A', 'B')));
		$result = $mapResponseProcessor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(1.5, $result->getValue());
		
		$state['response1'][] = new Pair('C', 'D');
		$result = $mapResponseProcessor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(4, $result->getValue());
		
		// mapEntries must be taken into account only once, as per QTI 2.1 spec.
		$state['response1'][] = new Pair('C', 'D');
		$result = $mapResponseProcessor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
		$this->assertEquals(4, $result->getValue()); // 2.5 taken into account only once!
	}
	
	public function testIndentifier() {
	    $variableDeclaration = $this->createComponentFromXml('
	        <responseDeclaration identifier="RESPONSE" cardinality="single" baseType="identifier">
                <correctResponse>
                    <value>Choice_3</value>
                </correctResponse>
                <mapping defaultValue="0">
                    <mapEntry mapKey="Choice_3" mappedValue="6"/>
                    <mapEntry mapKey="Choice_4" mappedValue="3"/>
                </mapping>
            </responseDeclaration>
	    ');
	    $variable = ResponseVariable::createFromDataModel($variableDeclaration);
	    $variable->setValue(new Identifier('Choice_3'));
	    $mapResponseExpr = $this->createComponentFromXml('<mapResponse identifier="RESPONSE"/>');
	    $mapResponseProcessor = new MapResponseProcessor($mapResponseExpr);
	    $mapResponseProcessor->setState(new State(array($variable)));
	    $result = $mapResponseProcessor->process();
	    $this->assertEquals(6.0, $result->getValue());
	                    
	}
	
	public function testVariableNotDefined() {
		$this->setExpectedException("qtism\\runtime\\expressions\\ExpressionProcessingException");
		$mapResponseExpr = $this->createComponentFromXml('<mapResponse identifier="INVALID"/>');
		$mapResponseProcessor = new MapResponseProcessor($mapResponseExpr);
		$mapResponseProcessor->process();
	}
	
	public function testNoMapping() {
		$this->setExpectedException("qtism\\runtime\\expressions\\ExpressionProcessingException");
		$variableDeclaration = $this->createComponentFromXml('<responseDeclaration identifier="response1" baseType="duration" cardinality="multiple"/>');
		$variable = ResponseVariable::createFromDataModel($variableDeclaration);
		$mapResponseExpr = $this->createComponentFromXml('<mapResponse identifier="response1"/>');
		$mapResponseProcessor = new MapResponseProcessor($mapResponseExpr);
		
		$mapResponseProcessor->setState(new State(array($variable)));
		$mapResponseProcessor->process();
	}
	
	public function testMultipleCardinalityIdentifierToFloat() {
	    $responseDeclaration = $this->createComponentFromXml('
	        <responseDeclaration identifier="RESPONSE" baseType="identifier" cardinality="multiple">
                <correctResponse>
	                <value>Choice1</value>
	                <value>Choice6</value>
	                <value>Choice7</value>
	            </correctResponse>
	            <mapping>
	                <mapEntry mapKey="Choice1" mappedValue="2"/>
	                <mapEntry mapKey="Choice6" mappedValue="20"/>
                    <mapEntry mapKey="Choice9" mappedValue="20"/>
	                <mapEntry mapKey="Choice2" mappedValue="-20"/>
	                <mapEntry mapKey="Choice3" mappedValue="-20"/>
	                <mapEntry mapKey="Choice4" mappedValue="-20"/>
	                <mapEntry mapKey="Choice5" mappedValue="-20"/>
	                <mapEntry mapKey="Choice7" mappedValue="-20" caseSensitive="false"/>
	                <!-- no mapping for choice 8 -->
	            </mapping>
	        </responseDeclaration>
	    ');
	    
	    $mapResponseExpression = new MapResponse('RESPONSE');
	    $mapResponseProcessor = new MapResponseProcessor($mapResponseExpression);
	    $state = new State();
	    $mapResponseProcessor->setState($state);
	    
	    // State setup.
	    $responseVariable = ResponseVariable::createFromDataModel($responseDeclaration);
	    $state->setVariable($responseVariable);
	    
	    // RESPONSE is an empty container.
	    $this->assertTrue($responseVariable->isNull());
	    $result = $mapResponseProcessor->process();
	    $this->assertEquals(0.0, $result->getValue());
	    $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
	    
	    // RESPONSE is NULL.
	    $responseVariable->setValue(null);
	    $result = $mapResponseProcessor->process();
	    $this->assertEquals(0.0, $result->getValue());
	    $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $result);
	    
	    // RESPONSE is Choice 6, Choice 8.
	    // Note that Choice 8 has not mapping, the mapping's default value (0) must be then used.
	    $state['RESPONSE'] = new MultipleContainer(BaseType::IDENTIFIER, array(new Identifier('Choice6'), new Identifier('Choice8')));
	    $result = $mapResponseProcessor->process();
	    $this->assertEquals(20.0, $result->getValue());
	    
	    // Response is Choice 6, Choice 8, but the mapping's default values goes to -1.
	    $mapping = $responseDeclaration->getMapping();
	    $mapping->setDefaultValue(-1.0);
	    $responseVariable = ResponseVariable::createFromDataModel($responseDeclaration);
	    $state->setVariable($responseVariable);
	    $state['RESPONSE'] = new MultipleContainer(BaseType::IDENTIFIER, array(new Identifier('Choice6'), new Identifier('Choice8')));
	    $result = $mapResponseProcessor->process();
	    $this->assertEquals(19.0, $result->getValue());
	    
	    // Response is 'choice7', and 'identifierX'. choice7 is in lower case but its
	    // associated entry is case insensitive. It must be then matched.
	    // 'identifierX' will not be matched at all, the mapping's default value (still -1) will be used.
	    $state['RESPONSE'] = new MultipleContainer(BaseType::IDENTIFIER, array(new Identifier('choice7'), new Identifier('identifierX')));
	    $result = $mapResponseProcessor->process();
	    $this->assertEquals(-21.0, $result->getValue());
	    
	    // Empty state.
	    // An exception is raised because no RESPONSE variable found.
	    $state->reset();
	    $this->setExpectedException('qtism\\runtime\\expressions\ExpressionProcessingException');
	    $result = $mapResponseProcessor->process();
	}
	
	public function testOutcomeDeclaration() {
		$this->setExpectedException("qtism\\runtime\\expressions\\ExpressionProcessingException");
		$variableDeclaration = $this->createComponentFromXml('
			<outcomeDeclaration identifier="response1" baseType="integer" cardinality="multiple">
				<mapping>
					<mapEntry mapKey="0" mappedValue="0.0"/>
				</mapping>
			</outcomeDeclaration>
		');
		$variable = OutcomeVariable::createFromDataModel($variableDeclaration);
		$mapResponseExpr = $this->createComponentFromXml('<mapResponse identifier="response1"/>');
		$mapResponseProcessor = new MapResponseProcessor($mapResponseExpr);
		
		$mapResponseProcessor->setState(new State(array($variable)));
		$mapResponseProcessor->process();
	}
}