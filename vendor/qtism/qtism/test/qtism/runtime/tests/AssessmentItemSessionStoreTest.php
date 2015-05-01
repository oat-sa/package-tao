<?php

require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\runtime\tests\SessionManager;
use qtism\data\ExtendedAssessmentItemRef;
use qtism\runtime\tests\AssessmentItemSessionStore;
use qtism\runtime\tests\AssessmentItemSession;
use qtism\data\AssessmentItemRef;

class AssessmentItemSessionStoreTest extends QtiSmTestCase {
	
    public function testHasMultipleOccurences() {
        $sessionManager = new SessionManager();
        $itemRef1 = new ExtendedAssessmentItemRef('Q01', './Q01.xml');
        $store = new AssessmentItemSessionStore();
        
        // No session registered for $itemRef1.
        $this->assertFalse($store->hasMultipleOccurences($itemRef1));
        
        // A single session registered for $itemRef1.
        $session = new AssessmentItemSession($itemRef1, $sessionManager);
        $store->addAssessmentItemSession($session, 0);
        $this->assertFalse($store->hasMultipleOccurences($itemRef1));
        
        // Two session registered for $itemRef1.
        $session = new AssessmentItemSession($itemRef1, $sessionManager);
        $store->addAssessmentItemSession($session, 1);
        $this->assertTrue($store->hasMultipleOccurences($itemRef1));
        
        $this->assertTrue($store->hasAssessmentItemSession($itemRef1, 0));
        $this->assertFalse($store->hasAssessmentItemSession(new ExtendedAssessmentItemRef('Q02', './Q02.xml')));
    }
    
    public function testGetAllAssessmentItemSessions() {
        $itemRef1 = new ExtendedAssessmentItemRef('Q01', './Q01.xml');
        $itemRef2 = new ExtendedAssessmentItemRef('Q02', './Q02.xml');
        $itemRef3 = new ExtendedAssessmentItemRef('Q03', './Q03.xml');
        
        $sessionManager = new SessionManager();
        $store = new AssessmentItemSessionStore();
        $store->addAssessmentItemSession(new AssessmentItemSession($itemRef1, $sessionManager), 0);
        $store->addAssessmentItemSession(new AssessmentItemSession($itemRef1, $sessionManager), 1);
        $store->addAssessmentItemSession(new AssessmentItemSession($itemRef1, $sessionManager), 3);
        $this->assertEquals(3, count($store->getAllAssessmentItemSessions()));
        
        $store->addAssessmentItemSession(new AssessmentItemSession($itemRef2, $sessionManager), 0);
        $store->addAssessmentItemSession(new AssessmentItemSession($itemRef3, $sessionManager), 0);
        $this->assertEquals(5, count($store->getAllAssessmentItemSessions()));
    }
}