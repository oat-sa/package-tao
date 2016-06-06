<?php

use qtism\data\storage\xml\XmlCompactDocument;
use qtism\data\storage\LocalFileResolver;
use qtism\data\NavigationMode;
use qtism\data\storage\xml\XmlDocument;
use qtism\data\storage\xml\XmlStorageException;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class XmlCompactAssessmentDocumentTest extends QtiSmTestCase {
	
	public function testSchemaValid() {
		$doc = new DOMDocument('1.0', 'UTF-8');
		$file = self::samplesDir() . 'custom/interaction_mix_sachsen_compact.xml';
		$doc->load($file, LIBXML_COMPACT|LIBXML_NONET|LIBXML_XINCLUDE);
		
		$schema = dirname(__FILE__) . '/../../../../../qtism/data/storage/xml/schemes/qticompact_v1p0.xsd';
		$this->assertTrue($doc->schemaValidate($schema));
	}
	
	public function testLoad(XmlCompactDocument $doc = null) {
		if (empty($doc)) {
			
			$doc = new XmlCompactDocument('1.0');
			
			$file = self::samplesDir() . 'custom/interaction_mix_sachsen_compact.xml';
			$doc->load($file);
		}
		
		$doc->schemaValidate();

		$testParts = $doc->getDocumentComponent()->getTestParts();
		$this->assertEquals(1, count($testParts));
		$assessmentSections = $testParts['testpartID']->getAssessmentSections();
		$this->assertEquals(1, count($assessmentSections));
		$assessmentSection = $assessmentSections['Sektion_181865064'];
		$this->assertInstanceOf('qtism\\data\\ExtendedAssessmentSection', $assessmentSection);
		
		$assessmentItemRefs = $assessmentSections['Sektion_181865064']->getSectionParts();
		
		$itemCount = 0;
		foreach ($assessmentItemRefs as $k => $ref) {
			$this->assertInstanceOf('qtism\\data\\ExtendedAssessmentItemRef', $assessmentItemRefs[$k]);
			$this->assertTrue($assessmentItemRefs[$k]->hasResponseProcessing());
			$this->assertFalse($assessmentItemRefs[$k]->isTimeDependent());
			$this->assertFalse($assessmentItemRefs[$k]->isAdaptive());
			$itemCount++;
		}
		$this->assertEquals($itemCount, 13); // contains 13 assessmentItemRef elements.
		
		// Pick up 3 for a test...
		$assessmentItemRef = $assessmentItemRefs['Choicemultiple_871212949'];
		$this->assertEquals('Choicemultiple_871212949', $assessmentItemRef->getIdentifier());
		$responseDeclarations = $assessmentItemRef->getResponseDeclarations();
		$this->assertEquals(1, count($responseDeclarations));
		$this->assertEquals('RESPONSE_27966883', $responseDeclarations['RESPONSE_27966883']->getIdentifier());
		$outcomeDeclarations = $assessmentItemRef->getOutcomeDeclarations();
		$this->assertEquals(10, count($outcomeDeclarations));
		$this->assertEquals('MAXSCORE', $outcomeDeclarations['MAXSCORE']->getIdentifier());
	}
	
	public function testSave() {
		$doc = new XmlCompactDocument('1.0');
		$file = self::samplesDir() . 'custom/interaction_mix_sachsen_compact.xml';
		$doc->load($file);
		
		$file = tempnam('/tmp', 'qsm');
		$doc->save($file);
		$this->assertTrue(file_exists($file));
		
		$doc = new XmlCompactDocument('1.0');
		$doc->load($file);
		
		// retest content...
		$this->testLoad($doc);
		
		unlink($file);
		$this->assertFalse(file_exists($file));
	}
	
	public function testCreateFrom() {
		$doc = new XmlDocument('2.1');
		$file = self::samplesDir() . 'ims/tests/interaction_mix_sachsen/interaction_mix_sachsen.xml';
		$doc->load($file);
		
		$compactDoc = XmlCompactDocument::createFromXmlAssessmentTestDocument($doc);
		
		$file = tempnam('/tmp', 'qsm');
		$compactDoc->save($file);
		$this->assertTrue(file_exists($file));
		
		$compactDoc = new XmlCompactDocument('1.0');
		$compactDoc->load($file);
		$this->testLoad($compactDoc);
		unlink($file);
		$this->assertFalse(file_exists($file));
	}
	
	public function testCreateFormExploded(XmlCompactDocument $compactDoc = null) {
		$doc = new XmlDocument('2.1');
		$file = self::samplesDir() . 'custom/interaction_mix_saschen_assessmentsectionref/interaction_mix_sachsen.xml';
		$doc->load($file);
		$compactDoc = XmlCompactDocument::createFromXmlAssessmentTestDocument($doc, new LocalFileResolver());
		
		$this->assertInstanceOf('qtism\\data\\storage\\xml\\XmlCompactDocument', $compactDoc);
		$this->assertEquals('InteractionMixSachsen_1901710679', $compactDoc->getDocumentComponent()->getIdentifier());
		$this->assertEquals('Interaction Mix (Sachsen)', $compactDoc->getDocumentComponent()->getTitle());
		
		$outcomeDeclarations = $compactDoc->getDocumentComponent()->getOutcomeDeclarations();
		$this->assertEquals(2, count($outcomeDeclarations));
		$this->assertEquals('SCORE', $outcomeDeclarations['SCORE']->getIdentifier());
		
		$testParts = $compactDoc->getDocumentComponent()->getTestParts();
		$this->assertEquals(1, count($testParts));
		$this->assertEquals('testpartID', $testParts['testpartID']->getIdentifier());
		$this->assertEquals(NavigationMode::NONLINEAR, $testParts['testpartID']->getNavigationMode());
		
		$assessmentSections1stLvl = $testParts['testpartID']->getAssessmentSections();
		$this->assertEquals(1, count($assessmentSections1stLvl));
		$this->assertEquals('Container_45665458', $assessmentSections1stLvl['Container_45665458']->getIdentifier());
		
		$assessmentSections2ndLvl = $assessmentSections1stLvl['Container_45665458']->getSectionParts();
		$this->assertEquals(1, count($assessmentSections2ndLvl));
		$this->assertInstanceOf('qtism\\data\\ExtendedAssessmentSection', $assessmentSections2ndLvl['Sektion_181865064']);
		$this->assertEquals(0, count($assessmentSections2ndLvl['Sektion_181865064']->getRubricBlockRefs()));
		$this->assertEquals('Sektion_181865064', $assessmentSections2ndLvl['Sektion_181865064']->getIdentifier());
		
		$assessmentItemRefs = $assessmentSections2ndLvl['Sektion_181865064']->getSectionParts();
		$this->assertEquals(13, count($assessmentItemRefs));
		
		// Pick up 4 for a test...
		$assessmentItemRef = $assessmentItemRefs['Hotspot_278940407'];
		$this->assertInstanceOf('qtism\\data\\ExtendedAssessmentItemRef', $assessmentItemRef);
		$this->assertEquals('Hotspot_278940407', $assessmentItemRef->getIdentifier());
		$responseDeclarations = $assessmentItemRef->getResponseDeclarations();
		$this->assertEquals(1, count($responseDeclarations));
		$this->assertEquals('RESPONSE', $responseDeclarations['RESPONSE']->getIdentifier());
		$outcomeDeclarations = $assessmentItemRef->getOutcomeDeclarations();
		$this->assertEquals(5, count($outcomeDeclarations));
		$this->assertEquals('FEEDBACKBASIC', $outcomeDeclarations['FEEDBACKBASIC']->getIdentifier());
		
		$file = tempnam('/tmp', 'qsm');
		$compactDoc->save($file);
		$this->assertTrue(file_exists($file));
		
		$compactDoc = new XmlCompactDocument('1.0');
		$compactDoc->load($file);
		$compactDoc->schemaValidate();
		
		unlink($file);
		$this->assertFalse(file_exists($file));
	}
	
	public function testLoadRubricBlockRefs(XmlCompactDocument $doc = null) {
	    if (empty($doc) === true) {
	        $src = self::samplesDir() . 'custom/runtime/rubricblockref.xml';
	        $doc = new XmlCompactDocument();
	        $doc->load($src, true);
	    }
	    
	    // It validates !
	    $this->assertInstanceOf('qtism\\data\\AssessmentTest', $doc->getDocumentComponent());
	    
	    // Did we retrieve the section as ExtendedAssessmentSection objects?
	    $sections = $doc->getDocumentComponent()->getComponentsByClassName('assessmentSection');
	    $this->assertEquals(1, count($sections));
	    $this->assertInstanceOf('qtism\\data\\ExtendedAssessmentSection', $sections[0]);
	    
	    // Retrieve rubricBlockRefs.
	    $rubricBlockRefs = $doc->getDocumentComponent()->getComponentsByClassName('rubricBlockRef');
	    $this->assertEquals(1, count($rubricBlockRefs));
	    $rubricBlockRef = $rubricBlockRefs[0];
	    $this->assertInstanceOf('qtism\\data\\content\\RubricBlockRef', $rubricBlockRef);
	    $this->assertEquals('R01', $rubricBlockRef->getIdentifier());
	    $this->assertEquals('./R01.xml', $rubricBlockRef->getHref());
	}
	
	public function testSaveRubricBlockRefs() {
	    $src = self::samplesDir() . 'custom/runtime/rubricblockref.xml';
	    $doc = new XmlCompactDocument();
	    $doc->load($src);
	    
	    $file = tempnam('/tmp', 'qsm');
	    $doc->save($file);
	    
	    $this->assertTrue(file_exists($file));
	    $this->testLoadRubricBlockRefs($doc);
	    
	    unlink($file);
	    $this->assertFalse(file_exists($file));
	}
	
	public function testExplodeRubricBlocks() {
	    $src = self::samplesDir() . 'custom/runtime/rubricblockrefs_explosion.xml';
	    $doc = new XmlCompactDocument();
	    $doc->load($src, true);
	    $doc->setExplodeRubricBlocks(true);
	    
	    $file = tempnam('/tmp', 'qsm');
	    
	    $doc->save($file);
	    
	    // Are external rubricBlocks set?
	    $pathinfo = pathinfo($file);
	    
	    $path = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . 'rubricBlock_RB_S01_1.xml';
	    $this->assertTrue(file_exists($path));
	    unlink($path);
	    $this->assertFalse(file_exists($path));
	    
	    $path = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . 'rubricBlock_RB_S01_2.xml';
	    $this->assertTrue(file_exists($path));
	    unlink($path);
	    $this->assertFalse(file_exists($path));
	    
	    unlink($file);
	}
    
    public function testCreateFromAssessmentTestInvalidAssessmentItemRefResolution() {
        $this->setExpectedException(
            '\\qtism\\data\\storage\\xml\\XmlStorageException',
            "An error occured while unreferencing item reference with identifier 'Q01'."
        );

        $doc = new XmlDocument('2.1');
        $file = self::samplesDir() . 'custom/tests/invalidassessmentitemref.xml';
        $doc->load($file);
        $compactDoc = XmlCompactDocument::createFromXmlAssessmentTestDocument($doc, new LocalFileResolver());
    }
}
