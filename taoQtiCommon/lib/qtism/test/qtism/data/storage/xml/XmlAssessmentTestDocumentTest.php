<?php

use qtism\data\storage\xml\XmlDocument;
use qtism\data\AssessmentTest;
use qtism\data\storage\xml\XmlStorageException;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class XmlAssessmentTestDocumentTest extends QtiSmTestCase {
	
	public function testLoad() {
		$uri = dirname(__FILE__) . '/../../../../samples/ims/tests/interaction_mix_sachsen/interaction_mix_sachsen.xml';
		$doc = new XmlDocument('2.1');
		$doc->load($uri);
		
		$this->assertInstanceOf('qtism\\data\\storage\\xml\\XmlDocument', $doc);
		$this->assertInstanceOf('qtism\\data\\AssessmentTest', $doc->getDocumentComponent());
	}
	
	public function testLoadFileDoesNotExist() {
		// This file does not exist.
		$uri = dirname(__FILE__) . '/../../../../samples/invalid/abcd.xml';
		$doc = new XmlDocument('2.1');
		$this->setExpectedException('qtism\\data\\storage\\xml\\XmlStorageException');
		$doc->load($uri);
	}
	
	public function testLoadFileMalformed() {
		// This file contains malformed xml markup.
		$uri = dirname(__FILE__) . '/../../../../samples/invalid/malformed.xml';
		$doc = new XmlDocument('2.1');
		
		try {
			$doc->load($uri);
			$this->assertFalse(true); // An exception must have been thrown.
		}
		catch (XmlStorageException $e) {
			$this->assertInternalType('string', $e->getMessage());
			$this->assertInstanceOf('qtism\\data\\storage\\xml\\LibXmlErrorCollection', $e->getErrors());
			$this->assertGreaterThan(0, count($e->getErrors()));
		}
	}
	
	public function testLoadSimpleItemSessionControlOnTestPart() {
	    $doc = new XmlDocument('2.1');
	    $doc->load(self::samplesDir() . 'custom/simple_itemsessioncontrol_testpart.xml');
	    $testParts = $doc->getDocumentComponent()->getTestParts();
	    $this->assertTrue($testParts['testPartId']->hasItemSessionControl());
	    $this->assertInternalType('integer', 0, $testParts['testPartId']->getItemSessionControl()->getMaxAttempts());
	    $this->assertEquals(0, $testParts['testPartId']->getItemSessionControl()->getMaxAttempts());
	}
	
	public function testSaveSimpleItemSessionControlOnTestPart() {
	    $doc = new XmlDocument('2.1');
	    $doc->load(self::samplesDir() . 'custom/simple_itemsessioncontrol_testpart.xml');
	    $file = tempnam('/tmp', 'qsm');
	    $doc->save($file);
	    
	    $doc = new XmlDocument('2.1');
	    $doc->load($file);
	    $testParts = $doc->getDocumentComponent()->getTestParts();
	    $this->assertTrue($testParts['testPartId']->hasItemSessionControl());
	    $this->assertInternalType('integer', 0, $testParts['testPartId']->getItemSessionControl()->getMaxAttempts());
	    $this->assertEquals(0, $testParts['testPartId']->getItemSessionControl()->getMaxAttempts());
	    
	    unlink($file);
	}
	
	public function testFullyQualified() {
		$uri = dirname(__FILE__) . '/../../../../samples/custom/fully_qualified_assessmenttest.xml';
		$doc = new XmlDocument('2.1');
		$doc->load($uri);
		$doc->schemaValidate();
		
		$this->assertInstanceOf('qtism\\data\\storage\\xml\\XmlDocument', $doc);
		$this->assertInstanceOf('qtism\\data\\AssessmentTest', $doc->getDocumentComponent());
	}
	
	public function testItemSessionControls() {
	    $doc = new XmlDocument('2.1');
	    $doc->load(self::samplesDir() . 'custom/runtime/routeitem_itemsessioncontrols.xml');
	    
	    // Q01.
	    $q01 = $doc->getDocumentComponent()->getComponentByIdentifier('Q01');
	    $this->assertInstanceOf('qtism\\data\\AssessmentItemRef', $q01);
	    $this->assertEquals(2, $q01->getItemSessionControl()->getMaxAttempts());
	    
	    // P02.
	    $p02 = $doc->getDocumentComponent()->getComponentByIdentifier('P02');
	    $this->assertInstanceOf('qtism\\data\\TestPart', $p02);
	    $this->assertEquals(4, $p02->getItemSessionControl()->getMaxAttempts());
	}
	
	private static function decorateUri($uri) {
		return dirname(__FILE__) . '/../../../../samples/ims/tests/' . $uri;
	}
}