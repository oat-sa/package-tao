<?php
require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\runtime\storage\binary\BinaryAssessmentTestSeeker;
use qtism\common\datatypes\files\FileSystemFile;
use qtism\common\datatypes\Duration;
use qtism\common\datatypes\Identifier;
use qtism\runtime\tests\SessionManager;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\DirectedPair;
use qtism\common\datatypes\Point;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\State;
use qtism\runtime\common\MultipleContainer;
use qtism\data\QtiComponentIterator;
use qtism\runtime\tests\AssessmentItemSessionState;
use qtism\runtime\tests\AssessmentTestSessionState;
use qtism\data\storage\xml\XmlCompactDocument;
use qtism\runtime\storage\binary\TemporaryQtiBinaryStorage;

class TemporaryQtiBinaryStorageTest extends QtiSmTestCase {
    
    public function testTemporaryQtiBinaryStorage() {
    
        $doc = new XmlCompactDocument();
        $doc->load(self::samplesDir() . 'custom/runtime/itemsubset.xml');
        $test = $doc->getDocumentComponent();
    
        $sessionManager = new SessionManager();
        $storage = new TemporaryQtiBinaryStorage($sessionManager, new BinaryAssessmentTestSeeker($doc->getDocumentComponent()));
        $session = $storage->instantiate($test);
        $sessionId = $session->getSessionId();
    
        $this->assertInstanceOf('qtism\\runtime\\tests\\AssessmentTestSession', $session);
        $this->assertEquals(AssessmentTestSessionState::INITIAL, $session->getState());
    
        $session->beginTestSession();
        $storage->persist($session);
    
        $session = $storage->retrieve($test, $sessionId);
        $this->assertEquals(AssessmentTestSessionState::INTERACTING, $session->getState());
    
        // The test session has begun. We are in linear mode so that all
        // item sessions must be initialized and selected for presentation
        // to the candidate.
        $itemSessionStore = $session->getAssessmentItemSessionStore();
        $itemSessionCount = 0;
        $iterator = new QtiComponentIterator($doc->getDocumentComponent(), array('assessmentItemRef'));
        foreach ($iterator as $itemRef) {
            $refItemSessions = $itemSessionStore->getAssessmentItemSessions($itemRef);
            foreach ($refItemSessions as $refItemSession) {
                $this->assertEquals(AssessmentItemSessionState::INITIAL, $refItemSession->getState());
                $itemSessionCount++;
            }
        }
        $this->assertEquals(9, $itemSessionCount);
    
        // The outcome variables composing the test-level global scope
        // must be set with their default value if any.
        foreach ($doc->getDocumentComponent()->getOutcomeDeclarations() as $outcomeDeclaration) {
            $this->assertFalse(is_null($session[$outcomeDeclaration->getIdentifier()]));
            $this->assertEquals(0, $session[$outcomeDeclaration->getIdentifier()]->getValue());
        }
    
        // Q01 - Correct response.
        $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $session['Q01.scoring']);
        $this->assertEquals(0.0, $session['Q01.scoring']->getValue());
        $this->assertSame(null, $session['Q01.RESPONSE']);
    
        $session->beginAttempt();
        sleep(1);
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
        $this->assertTrue($session['itemsubset.duration']->round()->equals(new Duration('PT1S')));
        $this->assertTrue($session['P01.duration']->round()->equals(new Duration('PT1S')));
        $this->assertTrue($session['S01.duration']->round()->equals(new Duration('PT1S')));
        $this->assertTrue($session['S02.duration']->round()->equals(new Duration('PT0S')));
        $this->assertTrue($session['S03.duration']->round()->equals(new Duration('PT0S')));
        $this->assertTrue($session['Q01.duration']->round()->equals(new Duration('PT1S')));
        
        $session->moveNext();
    
        // Because Q01 is not a multi-occurence item in the route, isLastOccurenceUpdate always return false.
        $this->assertFalse($session->isLastOccurenceUpdate($session->getCurrentAssessmentItemRef(), 0));
    
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
    
        $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $session['Q01.scoring']);
        $this->assertEquals(1.0, $session['Q01.scoring']->getValue());
        $this->assertEquals('ChoiceA', $session['Q01.RESPONSE']->getValue());
    
        // Q02 - Incorrect response.
        $this->assertEquals('Q02', $session->getCurrentAssessmentItemRef()->getIdentifier());
        $this->assertEquals('S01', $session->getCurrentAssessmentSection()->getIdentifier());
        $this->assertEquals('P01', $session->getCurrentTestPart()->getIdentifier());
        $session->beginAttempt();
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::MULTIPLE, BaseType::PAIR, new MultipleContainer(BaseType::PAIR, array(new Pair('C', 'M')))))));
        $session->moveNext();
    
        $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $session['Q02.SCORE']);
        $this->assertEquals(1.0, $session['Q02.SCORE']->getValue());
    
        // Q03 - Skip.
        $session->beginAttempt();
        $session->skip();
        $session->moveNext();
    
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
    
        // Q04 - Correct response.
        $this->assertEquals('Q04', $session->getCurrentAssessmentItemRef()->getIdentifier());
        $this->assertEquals('S02', $session->getCurrentAssessmentSection()->getIdentifier());
        $this->assertEquals('P01', $session->getCurrentTestPart()->getIdentifier());
        $this->assertEquals(AssessmentTestSessionState::INTERACTING, $session->getState());
        $session->beginAttempt();
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::MULTIPLE, BaseType::DIRECTED_PAIR, new MultipleContainer(BaseType::DIRECTED_PAIR, array(new DirectedPair('W', 'G1'), new DirectedPair('Su', 'G2')))))));
        $session->moveNext();
    
        $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $session['Q04.SCORE']);
        $this->assertEquals(3.0, $session['Q04.SCORE']->getValue());
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
        $this->assertTrue($session['Q04.RESPONSE']->equals(new MultipleContainer(BaseType::DIRECTED_PAIR, array(new DirectedPair('W', 'G1'), new DirectedPair('Su', 'G2')))));
    
        // Q05 - Skip.
        $session->beginAttempt();
        $session->skip();
        $session->moveNext();
    
        // Q06 - Skip.
        $session->beginAttempt();
        $session->skip();
        $session->moveNext();
    
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
    
        // Q07.1 - Incorrect response (but inside the circle).
        $this->assertFalse($session->isLastOccurenceUpdate($session->getCurrentAssessmentItemRef(), 0));
        $this->assertEquals('Q07', $session->getCurrentAssessmentItemRef()->getIdentifier());
        $this->assertEquals(0, $session->getCurrentAssessmentItemRefOccurence());
        $session->beginAttempt();
        sleep(1);
        $s02Duration = $session['S02.duration'];
        $this->assertTrue($session['S03.duration']->round()->equals(new Duration('PT1S')));
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::POINT, new Point(103, 114)))));
        $session->moveNext();
    
        // We now test the lastOccurence update for this multi-occurence item.
        $this->assertTrue($session->isLastOccurenceUpdate($session->getCurrentAssessmentItemRef(), 0));
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
        $this->assertTrue($session->isLastOccurenceUpdate($session->getCurrentAssessmentItemRef(), 0));
        $this->assertFalse($session->isLastOccurenceUpdate($session->getCurrentAssessmentItemRef(), 1));
    
        // Q07.2 Incorrect response (outside the circle).
        $this->assertEquals('Q07', $session->getCurrentAssessmentItemRef()->getIdentifier());
        $this->assertEquals(1, $session->getCurrentAssessmentItemRefOccurence());
        $session->beginAttempt();
        sleep(1);
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::POINT, new Point(200, 200)))));
        
        // Check that S02.duration was not updated.
        $this->assertTrue($session['S02.duration']->round()->equals($s02Duration->round()));
        $this->assertTrue($session['S03.duration']->round()->equals(new Duration('PT2S')));
        $session->moveNext();
    
        $this->assertFalse($session->isLastOccurenceUpdate($session->getCurrentAssessmentItemRef(), 0));
        $this->assertTrue($session->isLastOccurenceUpdate($session->getCurrentAssessmentItemRef(), 1));
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
        $this->assertFalse($session->isLastOccurenceUpdate($session->getCurrentAssessmentItemRef(), 0));
        $this->assertTrue($session->isLastOccurenceUpdate($session->getCurrentAssessmentItemRef(), 1));
    
        // Q07.3 Correct response (perfectly on the point).
        $this->assertEquals('Q07', $session->getCurrentAssessmentItemRef()->getIdentifier());
        $this->assertEquals(2, $session->getCurrentAssessmentItemRefOccurence());
        $session->beginAttempt();
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::POINT, new Point(102, 113)))));
        $session->moveNext();
    
        // End of test, outcome processing performed.
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
        $this->assertEquals(AssessmentTestSessionState::CLOSED, $session->getState());
        $this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $session['NCORRECTS01']);
        $this->assertEquals(1, $session['NCORRECTS01']->getValue());
        $this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $session['NCORRECTS02']);
        $this->assertEquals(1, $session['NCORRECTS02']->getValue());
        $this->assertInstanceOf('qtism\\common\\datatypes\\Integer', $session['NCORRECTS03']);
        $this->assertEquals(1, $session['NCORRECTS03']->getValue());
        $this->assertEquals(6, $session['NINCORRECT']->getValue());
        $this->assertEquals(6, $session['NRESPONSED']->getValue());
        $this->assertEquals(9, $session['NPRESENTED']->getValue());
        $this->assertEquals(9, $session['NSELECTED']->getValue());
        $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $session['PERCENT_CORRECT']);
        $this->assertEquals(round(33.33333, 3), round($session['PERCENT_CORRECT']->getValue(), 3));
    }
    
    public function testLinearNavigationSimultaneousSubmission() {
    
        $doc = new XmlCompactDocument();
        $doc->load(self::samplesDir() . 'custom/runtime/itemsubset_simultaneous.xml');
        $test = $doc->getDocumentComponent();
        
        $factory = new SessionManager($doc->getDocumentComponent());
        $storage = new TemporaryQtiBinaryStorage($factory, new BinaryAssessmentTestSeeker($doc->getDocumentComponent()));
        $sessionId = 'linearSimultaneous1337';
        $session = $storage->instantiate($doc->getDocumentComponent(), $sessionId);
        $session->beginTestSession();
    
        // Nothing in pending responses. The test has just begun.
        $this->assertEquals(0, count($session->getPendingResponseStore()->getAllPendingResponses()));
    
        // Q01 - Correct
        $session->beginAttempt();
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier('ChoiceA')))));
        $session->moveNext();
    
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
        $this->assertEquals(1, count($session->getPendingResponseStore()->getAllPendingResponses()));
        $this->assertEquals('ChoiceA', $session['Q01.RESPONSE']->getValue());
        $this->assertEquals(0.0, $session['Q01.scoring']->getValue());
    
        // Q02 - Correct
        $session->beginAttempt();
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::MULTIPLE, BaseType::PAIR, new MultipleContainer(BaseType::PAIR, array(new Pair('A', 'P'), new Pair('C', 'M'), new Pair('D', 'L')))))));
        $session->moveNext();
    
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
        $this->assertTrue($session['Q02.RESPONSE']->equals(new MultipleContainer(BaseType::PAIR, array(new Pair('A', 'P'), new Pair('C', 'M'), new Pair('D', 'L')))));
        $this->assertEquals(0.0, $session['Q02.SCORE']->getValue());
        $this->assertEquals(2, count($session->getPendingResponseStore()->getAllPendingResponses()));
    
        // Q03 - Skip
        $session->beginAttempt();
        $session->skip();
        $session->moveNext();
    
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
        $this->assertEquals(3, count($session->getPendingResponseStore()->getAllPendingResponses()));
    
        // Q04 - Skip
        $session->beginAttempt();
        $session->skip();
        $session->moveNext();
    
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
        $this->assertEquals(4, count($session->getPendingResponseStore()->getAllPendingResponses()));
    
        // Q05 - Skip
        $session->beginAttempt();
        $session->skip();
        $session->moveNext();
    
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
        $this->assertEquals(5, count($session->getPendingResponseStore()->getAllPendingResponses()));
    
        // Q06 - Skip
        $session->beginAttempt();
        $session->skip();
        $session->moveNext();
    
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
        $this->assertEquals(6, count($session->getPendingResponseStore()->getAllPendingResponses()));
    
        // Q07.1 - Correct
        $session->beginAttempt();
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::POINT, new Point(102, 113)))));
        $session->moveNext();
    
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
        $this->assertEquals(7, count($session->getPendingResponseStore()->getAllPendingResponses()));
        $this->assertTrue($session['Q07.1.RESPONSE']->equals(new Point(102, 113)));
        $this->assertEquals(0.0, $session['Q07.1.SCORE']->getValue());
    
        // Q07.2 - Incorrect but in the circle
        $session->beginAttempt();
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::POINT, new Point(103, 114)))));
        $session->moveNext();
    
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
        $this->assertEquals(8, count($session->getPendingResponseStore()->getAllPendingResponses()));
        $this->assertTrue($session['Q07.2.RESPONSE']->equals(new Point(103, 114)));
        $this->assertEquals(0.0, $session['Q07.2.SCORE']->getValue());
    
        // Q07.3 - Incorrect and out of the circle
        $session->beginAttempt();
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::POINT, new Point(30, 13)))));
        $session->moveNext();
    
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
    
        // Response processing should have taken place beauce this is the end of the current test part.
        // The Pending Response Store should be then flushed and now empty.
        $this->assertEquals(0, count($session->getPendingResponseStore()->getAllPendingResponses()));
        $this->assertTrue($session['Q07.3.RESPONSE']->equals(new Point(30, 13)));
        $this->assertEquals(0.0, $session['Q07.3.SCORE']->getValue());
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
    
        // Let's check the overall Assessment Test Session state.
        $this->assertEquals(1.0, $session['Q01.scoring']->getValue());
        $this->assertEquals(4.0, $session['Q02.SCORE']->getValue());
        $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $session['Q03.SCORE']);
        $this->assertEquals(0.0, $session['Q03.SCORE']->getValue());
        $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $session['Q04.SCORE']);
        $this->assertEquals(0.0, $session['Q04.SCORE']->getValue());
        $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $session['Q05.SCORE']);
        $this->assertEquals(0.0, $session['Q05.SCORE']->getValue());
        $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $session['Q06.mySc0r3']);
        $this->assertEquals(0.0, $session['Q06.mySc0r3']->getValue());
        $this->assertEquals(1.0, $session['Q07.1.SCORE']->getValue());
        $this->assertEquals(1.0, $session['Q07.2.SCORE']->getValue());
        $this->assertInstanceOf('qtism\\common\\datatypes\\Float', $session['Q07.3.SCORE']);
        $this->assertEquals(0.0, $session['Q07.3.SCORE']->getValue());
        $this->assertEquals(2, $session['NCORRECTS01']->getValue());
        $this->assertEquals(0, $session['NCORRECTS02']->getValue());
        $this->assertEquals(1, $session['NCORRECTS03']->getValue());
        $this->assertEquals(6, $session['NINCORRECT']->getValue());
        $this->assertEquals(5, $session['NRESPONSED']->getValue());
        $this->assertEquals(9, $session['NPRESENTED']->getValue());
        $this->assertEquals(9, $session['NSELECTED']->getValue());
        $this->assertEquals(round(33.33333, 3), round($session['PERCENT_CORRECT']->getValue(), 3));
    }
    
    public function testNonLinear() {
        $doc = new XmlCompactDocument();
        $doc->load(self::samplesDir() . 'custom/runtime/jumps.xml');
        $test = $doc->getDocumentComponent();
        
        $sessionManager = new SessionManager($doc->getDocumentComponent());
        $storage = new TemporaryQtiBinaryStorage($sessionManager, new BinaryAssessmentTestSeeker($test));
        $session = $storage->instantiate($test);
        $session->beginTestSession();
        $sessionId = $session->getSessionId();
        
        $storage->persist($session);
        $session = $storage->retrieve($test, $sessionId);
    }
    
    public function testFiles() {
        $doc = new XmlCompactDocument();
        $doc->load(self::samplesDir() . 'custom/runtime/files/files.xml');
        $test = $doc->getDocumentComponent();
        
        $sessionManager = new SessionManager($doc->getDocumentComponent());
        $storage = new TemporaryQtiBinaryStorage($sessionManager, new BinaryAssessmentTestSeeker($test));
        $session = $storage->instantiate($test);
        $session->beginTestSession();
        $sessionId = $session->getSessionId();
        
        // --- Q01 - files_1.txt = ('text.txt', 'text/plain', 'Some text...')
        $session->beginAttempt();
        $filepath = self::samplesDir() . 'datatypes/file/files_1.txt';
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::FILE, FileSystemFile::retrieveFile($filepath)))));
        $session->moveNext();
        $this->assertInstanceOf('qtism\\common\\datatypes\\File', $session['Q01.RESPONSE']);
        $this->assertEquals('text.txt', $session['Q01.RESPONSE']->getFilename());
        $this->assertEquals('text/plain', $session['Q01.RESPONSE']->getMimeType());
        $this->assertEquals('Some text...', $session['Q01.RESPONSE']->getData());
        
        // Let's persist and retrieve and look if we have the same value in Q01.RESPONSE.
        $storage->persist($session);
        unset($session);
        $session = $storage->retrieve($test, $sessionId);
        $this->assertInstanceOf('qtism\\common\\datatypes\\File', $session['Q01.RESPONSE']);
        $this->assertEquals('text.txt', $session['Q01.RESPONSE']->getFilename());
        $this->assertEquals('text/plain', $session['Q01.RESPONSE']->getMimeType());
        $this->assertEquals('Some text...', $session['Q01.RESPONSE']->getData());
        
        // --- Q02 - files_2.txt = ('', 'text/html', '<img src="/qtism/img.png"/>')
        $session->beginAttempt();
        $filepath = self::samplesDir() . 'datatypes/file/files_2.txt';
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::FILE, FileSystemFile::retrieveFile($filepath)))));
        $session->moveNext();
        $this->assertInstanceOf('qtism\\common\\datatypes\\File', $session['Q02.RESPONSE']);
        $this->assertEquals('', $session['Q02.RESPONSE']->getFilename());
        $this->assertEquals('text/html', $session['Q02.RESPONSE']->getMimeType());
        $this->assertEquals('<img src="/qtism/img.png"/>', $session['Q02.RESPONSE']->getData());
        
        // Again, we persist and retrieve.
        $storage->persist($session);
        unset($session);
        $session = $storage->retrieve($test, $sessionId);
        
        // We now test all the collected variables.
        $this->assertInstanceOf('qtism\\common\\datatypes\\File', $session['Q01.RESPONSE']);
        $this->assertEquals('text.txt', $session['Q01.RESPONSE']->getFilename());
        $this->assertEquals('text/plain', $session['Q01.RESPONSE']->getMimeType());
        $this->assertEquals('Some text...', $session['Q01.RESPONSE']->getData());
        
        $this->assertInstanceOf('qtism\\common\\datatypes\\File', $session['Q02.RESPONSE']);
        $this->assertEquals('', $session['Q02.RESPONSE']->getFilename());
        $this->assertEquals('text/html', $session['Q02.RESPONSE']->getMimeType());
        $this->assertEquals('<img src="/qtism/img.png"/>', $session['Q02.RESPONSE']->getData());
        
        // --- Q03 - files_3.txt ('empty.txt', 'text/plain', '')
        $session->beginAttempt();
        $filepath = self::samplesDir() . 'datatypes/file/files_3.txt';
        $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::FILE, FileSystemFile::retrieveFile($filepath)))));
        $session->moveNext();
        $this->assertFalse($session->isRunning());
        $this->assertInstanceOf('qtism\\common\\datatypes\\File', $session['Q02.RESPONSE']);
        $this->assertEquals('empty.txt', $session['Q03.RESPONSE']->getFilename());
        $this->assertEquals('text/plain', $session['Q03.RESPONSE']->getMimeType());
        $this->assertEquals('', $session['Q03.RESPONSE']->getData());
        
        $storage->persist($session);
        unset($session);
        $session = $storage->retrieve($test, $sessionId);
        
        // Final big check.
        $this->assertInstanceOf('qtism\\common\\datatypes\\File', $session['Q01.RESPONSE']);
        $this->assertEquals('text.txt', $session['Q01.RESPONSE']->getFilename());
        $this->assertEquals('text/plain', $session['Q01.RESPONSE']->getMimeType());
        $this->assertEquals('Some text...', $session['Q01.RESPONSE']->getData());
        
        $this->assertInstanceOf('qtism\\common\\datatypes\\File', $session['Q02.RESPONSE']);
        $this->assertEquals('', $session['Q02.RESPONSE']->getFilename());
        $this->assertEquals('text/html', $session['Q02.RESPONSE']->getMimeType());
        $this->assertEquals('<img src="/qtism/img.png"/>', $session['Q02.RESPONSE']->getData());
        
        $this->assertInstanceOf('qtism\\common\\datatypes\\File', $session['Q02.RESPONSE']);
        $this->assertEquals('empty.txt', $session['Q03.RESPONSE']->getFilename());
        $this->assertEquals('text/plain', $session['Q03.RESPONSE']->getMimeType());
        $this->assertEquals('', $session['Q03.RESPONSE']->getData());
    }
}
