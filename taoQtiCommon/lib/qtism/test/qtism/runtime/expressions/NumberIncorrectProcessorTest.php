<?php

use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\Point;
use qtism\common\datatypes\DirectedPair;
use qtism\common\datatypes\Pair;
use qtism\runtime\common\MultipleContainer;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\State;
use qtism\common\collections\IdentifierCollection;
use qtism\data\expressions\NumberIncorrect;
use qtism\runtime\expressions\NumberIncorrectProcessor;

require_once (dirname(__FILE__) . '/../../../QtiSmItemSubsetTestCase.php');

class NumberIncorrectProcessorTest extends QtiSmItemSubsetTestCase {
	
	public function testNumberIncorrect() {
		$session = $this->getTestSession();
		
		$overallCorrect = self::getNumberIncorrect();
		$includeMathResponded = self::getNumberIncorrect('', new IdentifierCollection(array('mathematics')));
		$processor = new NumberIncorrectProcessor($overallCorrect);
		$processor->setState($session);
		
		// Nothing responded yet.
		$this->assertEquals(0, $processor->process()->getValue());
		$processor->setExpression($includeMathResponded);
		$this->assertEquals(0, $processor->process()->getValue());
		
		// Q01
		$session->beginAttempt();
		$responses = new State();
		// Correct!
		$responses->setVariable(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')));
		$session->endAttempt($responses);
		$processor->setExpression($overallCorrect);
	    $this->assertEquals(0, $processor->process()->getValue());
	    $processor->setExpression($includeMathResponded);
	    $this->assertEquals(0, $processor->process()->getValue());
	    $session->moveNext();
	    
	    // Q02
	    $responses->reset();
	    $session->beginAttempt();
	    // Incorrect!
	    $responses->setVariable(new ResponseVariable('RESPONSE', Cardinality::MULTIPLE, BaseType::PAIR, new MultipleContainer(BaseType::PAIR, array(new Pair('A', 'P'), new Pair('D', 'L')))));
	    $session->endAttempt($responses);
	    $processor->setExpression($overallCorrect);
	    $this->assertEquals(1, $processor->process()->getValue());
	    $processor->setExpression($includeMathResponded);
	    $this->assertEquals(0, $processor->process()->getValue());
	    $session->moveNext();
	    
	    // Q03
	    // Incorrect!
	    $session->beginAttempt();
	    $session->skip();
	    $processor->setExpression($overallCorrect);
	    $this->assertEquals(2, $processor->process()->getValue());
	    $session->moveNext();
	    
	    // Q04
	    // Correct!
	    $responses->reset();
	    $session->beginAttempt();
	    $responses->setVariable(new ResponseVariable('RESPONSE', Cardinality::MULTIPLE, BaseType::DIRECTED_PAIR, new MultipleContainer(BaseType::DIRECTED_PAIR, array(new DirectedPair('W', 'G1'), new DirectedPair('Su', 'G2')))));
	    $session->endAttempt($responses);
	    $this->assertEquals(2, $processor->process()->getValue());
	    $session->moveNext();
	    
	    // Q05
	    // Incorrect!
	    $session->beginAttempt();
	    $session->skip();
	    $this->assertEquals(3, $processor->process()->getValue());
	    $session->moveNext();
	    
	    // Q06
	    // Correct!
	    $responses->reset();
	    $session->beginAttempt();
	    $responses->setVariable(new ResponseVariable('answer', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('A')));
	    $session->endAttempt($responses);
	    $this->assertEquals(3, $processor->process()->getValue());
	    $session->moveNext();
	    
	    // Q07.1
	    // Incorrect
	    $responses->reset();
	    $session->beginAttempt();
	    $responses->setVariable(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::POINT, new Point(100, 100)));
	    $session->endAttempt($responses);
	    $this->assertEquals(4, $processor->process()->getValue());
	    $session->moveNext();
	    
	    // Q07.2
	    // Incorrect!
	    $responses->reset();
	    $session->beginAttempt();
	    $responses->setVariable(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::POINT, new Point(10, 10)));
	    $session->endAttempt($responses);
	    $this->assertEquals(5, $processor->process()->getValue());
	    $session->moveNext();
	    
	    // Q07.3
	    // Correct!
	    $responses->reset();
	    $session->beginAttempt();
	    $responses->setVariable(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::POINT, new Point(102, 113)));
	    $session->endAttempt($responses);
	    $this->assertEquals(5, $processor->process()->getValue());
	    $session->moveNext();
	}
	
    protected static function getNumberIncorrect($sectionIdentifier = '', IdentifierCollection $includeCategories = null, IdentifierCollection $excludeCategories = null) {
	    $numberIncorrect = new NumberIncorrect();
	    $numberIncorrect->setSectionIdentifier($sectionIdentifier);
	    
	    if (empty($includeCategories) === false) {
	        $numberIncorrect->setIncludeCategories($includeCategories);
	    }
	    
	    if (empty($excludeCategories) === false) {
	        $numberIncorrect->setExcludeCategories($excludeCategories);
	    }

	    return $numberIncorrect;
	}
}