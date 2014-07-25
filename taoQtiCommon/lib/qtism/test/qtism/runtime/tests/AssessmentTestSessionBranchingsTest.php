<?php

use qtism\common\datatypes\Identifier;
use qtism\runtime\tests\AssessmentTestSessionState;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\State;
use qtism\runtime\tests\SessionManager;
use qtism\runtime\tests\AssessmentTestSession;
use qtism\data\storage\xml\XmlCompactDocument;

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

class AssessmentTestSessionBranchingsTest extends QtiSmTestCase {
	
    public function testInstantiationSample1() {
        
        $doc = new XmlCompactDocument('1.0');
        $doc->load(self::samplesDir() . 'custom/runtime/branchings/branchings_single_section_linear.xml');
        
        $manager = new SessionManager();
        $testSession = $manager->createAssessmentTestSession($doc->getDocumentComponent());
        
        $route = $testSession->getRoute();
        
        // $routeItemQ01 must have a single branchRule targeting Q03.
        $routeItemQ01 = $route->getRouteItemAt(0);
        $branchRules = $routeItemQ01->getBranchRules();
        $this->assertEquals(1, count($branchRules));
        $this->assertEquals('Q03', $branchRules[0]->getTarget());
        
        // $routeItemQ02 must have a single branchRule targeting Q04.
        $routeItemQ02 = $route->getRouteItemAt(1);
        $branchRules = $routeItemQ02->getBranchRules();
        $this->assertEquals(1, count($branchRules));
        $this->assertEquals('Q04', $branchRules[0]->getTarget());
        
        // $routeItemQ03 must have a single branchRule targeting EXIT_TEST
        $routeItemQ03 = $route->getRouteItemAt(2);
        $branchRules = $routeItemQ03->getBranchRules();
        $this->assertEquals(1, count($branchRules));
        $this->assertEquals('EXIT_TEST', $branchRules[0]->getTarget());
        
        // $routeItemQ04 is the end of the test and has no branchRules.
        $routeItemQ04 = $route->getRouteItemAt(3);
        $this->assertEquals(0, count($routeItemQ04->getBranchRules()));
    }
    
    public function testBranchingSingleSectionLinear1() {
        $doc = new XmlCompactDocument('1.0');
        $doc->load(self::samplesDir() . 'custom/runtime/branchings/branchings_single_section_linear.xml');
        
        $manager = new SessionManager();
        $testSession = $manager->createAssessmentTestSession($doc->getDocumentComponent());
        $testSession->beginTestSession();
        
        // Q01 - We answer correct to bypass Q02.
        $testSession->beginAttempt();
        $responses = new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA'))));
        $testSession->endAttempt($responses);
        
        // Correct? Then we should go to Q03.
        $this->assertEquals(1.0, $testSession['Q01.SCORE']->getValue());
        $testSession->moveNext();
        
        // Q03 - Are we there? We answer incorrect to take Q04.
        $this->assertEquals('Q03', $testSession->getCurrentAssessmentItemRef()->getIdentifier());
        $testSession->beginAttempt();
        $responses = new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceZ'))));
        $testSession->endAttempt($responses);
        $testSession->moveNext();
        
        // Q04 - Last item, nothing special.
        $this->assertEquals('Q04', $testSession->getCurrentAssessmentItemRef()->getIdentifier());
        $testSession->beginAttempt();
        $responses = new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceD'))));
        $testSession->endAttempt($responses);
        $testSession->moveNext();
        
        // Test the global scope.
        $this->assertFalse($testSession->isRunning());
        
        $this->assertEquals(1.0, $testSession['Q01.SCORE']->getValue());
        $this->assertSame(null, $testSession['Q02.SCORE']); // Not eligible.
        $this->assertEquals(0.0, $testSession['Q03.SCORE']->getValue());
        $this->assertEquals(1.0, $testSession['Q04.SCORE']->getValue());
    }
    
    public function testBranchingSingleSectionLinear2() {
        $doc = new XmlCompactDocument('1.0');
        $doc->load(self::samplesDir() . 'custom/runtime/branchings/branchings_single_section_linear.xml');
        
        $manager = new SessionManager();
        $testSession = $manager->createAssessmentTestSession($doc->getDocumentComponent());
        $testSession->beginTestSession();
        
        // Q01 - We answer correct to move to Q03.
        $testSession->beginAttempt();
        $responses = new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA'))));
        $testSession->endAttempt($responses);
        $testSession->moveNext();
        
        // Q03 - We want to reach the EXIT_TEST target.
        $testSession->beginAttempt();
        $responses = new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceC'))));
        $testSession->endAttempt($responses);
        $testSession->moveNext();
        
        // We should have reached the end.
        $this->assertFalse($testSession->isRunning());
        $this->assertEquals($testSession->getState(), AssessmentTestSessionState::CLOSED);
        $this->assertEquals(1.0, $testSession['Q01.SCORE']->getValue());
        $this->assertSame(null, $testSession['Q02.SCORE']); // Not eligible.
        $this->assertEquals(1.0, $testSession['Q03.SCORE']->getValue());
        $this->assertSame(null, $testSession['Q04.SCORE']); // Not eligible.
    }
    
    public function testBranchingSingleSectionNonLinear1() {
        // This test only aims at testing if branch rules
        // are correctly ignored when the navigation mode is non linear.
        $doc = new XmlCompactDocument('1.0');
        $doc->load(self::samplesDir() . 'custom/runtime/branchings/branchings_single_section_nonlinear.xml');
        
        // Q01 - We answer correct. In linear mode we should go to Q03.
        // However, in non linear mode branch rules are ignored and we go then
        // to Q02.
        $manager = new SessionManager();
        $testSession = $manager->createAssessmentTestSession($doc->getDocumentComponent());
        $testSession->beginTestSession();
        
        $testSession->beginAttempt();
        $responses = new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA'))));
        $testSession->endAttempt($responses);
        $testSession->moveNext();
        
        $this->assertEquals('Q02', $testSession->getCurrentAssessmentItemRef()->getIdentifier());
    }
    
    /**
     * @dataProvider branchingMultipleOccurencesProvider
     */
    public function testBranchingMultipleOccurences($response, $expectedTarget, $occurence) {
        // This test aims at testing the possibility to jump
        // on a particular item ref occurence.
        $doc = new XmlCompactDocument('1.0');
        $doc->load(self::samplesDir() . 'custom/runtime/branchings/branchings_multiple_occurences.xml');
        
        $manager = new SessionManager();
        $testSession = $manager->createAssessmentTestSession($doc->getDocumentComponent());
        $testSession->beginTestSession();
        
        $testSession->beginAttempt();
        
        if (empty($response) === true) {
            $testSession->skip();
            $testSession->moveNext();
        }
        else {
            $testSession->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, $response))));
            $testSession->moveNext();
        }
        
        $this->assertEquals($expectedTarget, $testSession->getCurrentAssessmentItemRef()->getIdentifier());
        $this->assertEquals($occurence, $testSession->getCurrentAssessmentItemRefOccurence());
    }
    
    public function branchingMultipleOccurencesProvider() {
        return array(
            array(new Identifier('goto21'), 'Q02', 0),
            array(new Identifier('goto22'), 'Q02', 1),
            array(new Identifier('goto23'), 'Q02', 2),
            array(null, 'Q02', 3)              
        );
    }
}