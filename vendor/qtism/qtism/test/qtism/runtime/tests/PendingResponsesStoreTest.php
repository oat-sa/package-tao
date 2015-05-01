<?php
require_once (dirname(__FILE__) . '/../../../QtiSmTestCase.php');

use qtism\common\datatypes\Integer;
use qtism\common\datatypes\Boolean;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\State;
use qtism\runtime\common\ResponseVariable;
use qtism\data\AssessmentItemRef;
use qtism\runtime\tests\PendingResponseStore;
use qtism\runtime\tests\PendingResponses;
use qtism\runtime\tests\PendingResponsesCollection;

class PendingResponseStoreTest extends QtiSmTestCase {
    
    public function testPendingResponseStore() {
        $itemRef1 = new AssessmentItemRef('Q01', './Q01.xml');
        $itemRef2 = new AssessmentItemRef('Q02', './Q02.xml');
        $itemRef3 = new AssessmentItemRef('Q03', './Q02.xml');
        
        $state1 = new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::BOOLEAN, new Boolean(true))));
        $state2 = new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::BOOLEAN, new Boolean(false))));
        $state3 = new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::INTEGER, new Integer(1337))));
        
        $store = new PendingResponseStore();
        $store->addPendingResponses(new PendingResponses($state1, $itemRef1));
        $store->addPendingResponses(new PendingResponses($state2, $itemRef1, 1));
        
        $this->assertEquals(2, count($store->getAllPendingResponses()));
        
        $this->assertTrue($store->hasPendingResponses($itemRef1));
        $this->assertFalse($store->hasPendingResponses($itemRef3));
        $this->assertFalse($store->hasPendingResponses($itemRef1, 4));
        
        $this->assertTrue($itemRef1 === $store->getPendingResponses($itemRef1)->getAssessmentItemRef());
        $this->assertTrue($state2 === $store->getPendingResponses($itemRef1, 1)->getState());
    }
}