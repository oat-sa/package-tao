<?php

use qtism\data\expressions\Correct;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

use qtism\data\AssessmentItemRef;
use qtism\data\storage\xml\XmlCompactDocument;
use qtism\runtime\storage\common\AssessmentTestSeeker;

class AssessmentTestSeekerTest extends QtiSmTestCase {
	
    public function testSeekComponent() {
        
        $doc = new XmlCompactDocument();
        $doc->load(self::samplesDir() . 'custom/runtime/itemsubset.xml');
        
        $seeker = new AssessmentTestSeeker($doc->getDocumentComponent(), array('assessmentItemRef', 'assessmentSection'));
        
        $ref = $seeker->seekComponent('assessmentItemRef', 0);
        $this->assertEquals('Q01', $ref->getIdentifier());
        
        $ref = $seeker->seekComponent('assessmentItemRef', 3);
        $this->assertEquals('Q04', $ref->getIdentifier());
        
        $sec = $seeker->seekComponent('assessmentSection', 0);
        $this->assertEquals('S01', $sec->getIdentifier());
        
        $ref = $seeker->seekComponent('assessmentItemRef', 6);
        $this->assertEquals('Q07', $ref->getIdentifier());
        
        $sec = $seeker->seekComponent('assessmentSection', 2);
        $this->assertEquals('S03', $sec->getIdentifier());
        
        // Should not be found.
        try {
            $ref = $seeker->seekComponent('responseProcessing', 25);
            $this->assertFalse(true, "The 'responseProcessing' QTI class is not registered with the AssessmentTestSeeker object.");
        }
        catch (OutOfBoundsException $e) {
            $this->assertTrue(true);
        }
        
        try {
            $ref = $seeker->seekComponent('assessmentItemRef', 100);
            $this->assertFalse(true, "Nothing should be found for 'assessmentItemRef' at position '100'. This is out of bounds.");
        }
        catch (OutOfBoundsException $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testSeekPosition() {
        
        $doc = new XmlCompactDocument();
        $doc->load(self::samplesDir() . 'custom/runtime/itemsubset.xml');
        
        $seeker = new AssessmentTestSeeker($doc->getDocumentComponent(), array('assessmentItemRef', 'assessmentSection'));
        
        $this->assertEquals(1, $seeker->seekPosition($doc->getDocumentComponent()->getComponentByIdentifier('Q02')));
        $this->assertEquals(0, $seeker->seekPosition($doc->getDocumentComponent()->getComponentByIdentifier('Q01')));
        $this->assertEquals(0, $seeker->seekPosition($doc->getDocumentComponent()->getComponentByIdentifier('S01')));
        $this->assertEquals(2, $seeker->seekPosition($doc->getDocumentComponent()->getComponentByIdentifier('S03')));
        $this->assertEquals(2, $seeker->seekPosition($doc->getDocumentComponent()->getComponentByIdentifier('Q03')));
        $this->assertEquals(1, $seeker->seekPosition($doc->getDocumentComponent()->getComponentByIdentifier('S02')));
        
        try {
            $pos = $seeker->seekPosition(new AssessmentItemRef('Q05', 'Q05.xml'));
            $this->assertFalse(true, "Nothing should be found for Q05.");
        }
        catch (OutOfBoundsException $e) {
            $this->assertTrue(true);
        }
        
        try {
            $pos = $seeker->seekPosition(new Correct('Q01.SCORE'));
            $this->assertFalse(true, "The 'correct' QTI class is not registered with the AssessmentTestSeeker object.");
        }
        catch (OutOfBoundsException $e) {
            $this->assertTrue(true);
        }
    }
}
