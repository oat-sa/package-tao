<?php

use qtism\data\storage\xml\XmlDocument;

use qtism\data\View;
use qtism\data\AssessmentSection;
use qtism\data\storage\xml\XmlStorageException;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class XmlAssessmentSectionDocumentTest extends QtiSmTestCase {
	
	public function testLoad(AssessmentSection $assessmentSection = null) {
		
		if (empty($assessmentSection)) {
			$uri = self::samplesDir(). 'custom/standalone_assessmentsection.xml';
			$doc = new XmlDocument();
			$doc->load($uri);
			
			$this->assertInstanceOf('qtism\\data\\storage\\xml\\XmlDocument', $doc);
			$this->assertInstanceOf('qtism\\data\\AssessmentSection', $doc->getDocumentComponent());
			
			$assessmentSection = $doc->getDocumentComponent();
		}
		
		$rubricBlocks = $assessmentSection->getRubricBlocks();
		$this->assertInstanceOf('qtism\\data\\content\\RubricBlockCollection', $rubricBlocks);
		$this->assertEquals(1, count($rubricBlocks));
		
		$rubricBlock = $rubricBlocks[0];
		$views = $rubricBlock->getViews();
		$this->assertEquals(1, count($views));
		$this->assertEquals(View::CANDIDATE, $views[0]);
		
		$assessmentItemRefs = $assessmentSection->getSectionParts();
		$this->assertInstanceOf('qtism\\data\\SectionPartCollection', $assessmentItemRefs);
		
		foreach ($assessmentItemRefs as $itemRef) {
			$this->assertInstanceOf('qtism\\data\\AssessmentItemRef', $itemRef);
		}
	}
	
	public function testWrite() {
		$uri = self::samplesDir() . 'custom/standalone_assessmentsection.xml';
		$doc = new XmlDocument();
		$doc->load($uri);
		
		$assessmentSection = $doc->getDocumentComponent();
		
		// Write the file.
		$uri = tempnam('/tmp', 'qsm');
		$doc->save($uri);
		$this->assertTrue(file_exists($uri));
		
		// Reload it.
		$doc->load($uri);
		$this->assertInstanceOf('qtism\\data\\AssessmentSection', $doc->getDocumentComponent());
		
		// Retest.
		$this->testLoad($doc->getDocumentComponent());
		
		unlink($uri);
	}
}