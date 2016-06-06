<?php

use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\data\storage\xml\XmlDocument;
use qtism\data\View;
use qtism\data\AssessmentItem;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class XmlAssessmentItemDocumentTest extends QtiSmTestCase {
	
	/**
	 * @dataProvider validFileProvider
	 */
	public function testLoad($uri) {
		$doc = new XmlDocument('2.1');
		$doc->load($uri);
		
		$assessmentItem = $doc->getDocumentComponent();
		$this->assertInstanceOf('qtism\\data\\AssessmentItem', $assessmentItem);
	}
    
    /**
	 * @dataProvider validFileProvider
	 */
    public function testLoadFromString($uri) {
        $doc = new XmlDocument('2.1');
		$doc->loadFromString(file_get_contents($uri));
		
		$assessmentItem = $doc->getDocumentComponent();
		$this->assertInstanceOf('qtism\\data\\AssessmentItem', $assessmentItem);
    }
	
	/**
	 * @dataProvider validFileProvider
	 */
	public function testWrite($uri) {
		$doc = new XmlDocument('2.1');
		$doc->load($uri);
		
		$assessmentItem = $doc->getDocumentComponent();
		$this->assertInstanceOf('qtism\\data\\AssessmentItem', $assessmentItem);
		
		$file = tempnam('/tmp', 'qsm');
		$doc->save($file);
		
		$this->assertTrue(file_exists($file));
		$this->testLoad($file);
		
		unlink($file);
		// Nobody else touched it?
		$this->assertFalse(file_exists($file));
	}
    
    /**
	 * @dataProvider validFileProvider
	 */
    public function testSaveToString($uri) {
        $doc = new XmlDocument('2.1');
		$doc->load($uri);
		
		$assessmentItem = $doc->getDocumentComponent();
		$this->assertInstanceOf('qtism\\data\\AssessmentItem', $assessmentItem);
		
		$file = tempnam('/tmp', 'qsm');
		file_put_contents($file, $doc->saveToString());
		
		$this->assertTrue(file_exists($file));
		$this->testLoadFromString($file);
		
		unlink($file);
		// Nobody else touched it?
		$this->assertFalse(file_exists($file));
    }
	
	public function testLoad21() {
		$file = self::samplesDir() . 'ims/items/2_1/associate.xml';
		$doc = new XmlDocument();
		$doc->load($file);
		
		$this->assertEquals('2.1', $doc->getVersion());
	}
	
	public function testLoad20() {
		$file = self::samplesDir() . 'ims/items/2_0/associate.xml';
		$doc = new XmlDocument();
		$doc->load($file);
		
		$this->assertEquals('2.0', $doc->getVersion());
	}
	
	public function testLoadTemplate($uri = '') {
	    $file = (empty($uri) === true) ? self::samplesDir() . 'ims/items/2_1/template.xml' : $uri;
	    
	    $doc = new XmlDocument();
	    $doc->load($file, true);
	    
	    $item = $doc->getDocumentComponent();
	    
	    // Look for all template declarations.
	    $templateDeclarations = $item->getTemplateDeclarations();
	    $this->assertEquals(4, count($templateDeclarations));
	    
	    $this->assertEquals('PEOPLE', $templateDeclarations['PEOPLE']->getIdentifier());
	    $this->assertEquals(Cardinality::SINGLE, $templateDeclarations['PEOPLE']->getCardinality());
	    $this->assertEquals(BaseType::STRING, $templateDeclarations['PEOPLE']->getBaseType());
	    $this->assertFalse($templateDeclarations['PEOPLE']->isMathVariable());
	    $this->assertFalse($templateDeclarations['PEOPLE']->isParamVariable());
	    
	    $this->assertEquals('A', $templateDeclarations['A']->getIdentifier());
	    $this->assertEquals(Cardinality::SINGLE, $templateDeclarations['A']->getCardinality());
	    $this->assertEquals(BaseType::INTEGER, $templateDeclarations['A']->getBaseType());
	    $this->assertFalse($templateDeclarations['A']->isMathVariable());
	    $this->assertFalse($templateDeclarations['A']->isParamVariable());
	    
	    $this->assertEquals('B', $templateDeclarations['B']->getIdentifier());
	    $this->assertEquals(Cardinality::SINGLE, $templateDeclarations['B']->getCardinality());
	    $this->assertEquals(BaseType::INTEGER, $templateDeclarations['B']->getBaseType());
	    $this->assertFalse($templateDeclarations['B']->isMathVariable());
	    $this->assertFalse($templateDeclarations['B']->isParamVariable());
	    
	    $this->assertEquals('MIN', $templateDeclarations['MIN']->getIdentifier());
	    $this->assertEquals(Cardinality::SINGLE, $templateDeclarations['MIN']->getCardinality());
	    $this->assertEquals(BaseType::INTEGER, $templateDeclarations['MIN']->getBaseType());
	    $this->assertFalse($templateDeclarations['MIN']->isMathVariable());
	    $this->assertFalse($templateDeclarations['MIN']->isParamVariable());
	}
	
	public function testWriteTemplate() {
	    $doc = new XmlDocument();
	    $doc->load(self::samplesDir() . 'ims/items/2_1/template.xml');
	    
	    $file = tempnam('/tmp', 'qsm');
	    $doc->save($file);
	    unset($doc);
	    
	    $this->testLoadTemplate($file);
	    
	    unlink($file);
	    $this->assertFalse(file_exists($file));
	}
	
	public function testLoadPCIItem($url = '') {
	    $doc = new XmlDocument();
	    $doc->load((empty($url) === true) ? self::samplesDir() . 'custom/interactions/custom_interaction_pci.xml' : $url, true);
	    $item = $doc->getDocumentComponent();
	    
	    $this->assertInstanceOf('qtism\\data\\AssessmentItem', $item);
	    $this->assertEquals('SimpleExample', $item->getIdentifier());
	    $this->assertEquals('Example', $item->getTitle());
	    $this->assertFalse($item->isAdaptive());
	    $this->assertFalse($item->isTimeDependent());
	    
	    // responseDeclaration
	    $responseDeclarations = $item->getComponentsByClassName('responseDeclaration');
	    $this->assertEquals(1, count($responseDeclarations));
	    $this->assertEquals(BaseType::POINT, $responseDeclarations[0]->getBaseType());
	    $this->assertEquals(Cardinality::SINGLE, $responseDeclarations[0]->getCardinality());
	    $this->assertEquals('RESPONSE', $responseDeclarations[0]->getIdentifier());
	    
	    // templateDeclarations
	    $templateDeclarations = $item->getComponentsByClassName('templateDeclaration');
	    $this->assertEquals(2, count($templateDeclarations));
	    $this->assertEquals(BaseType::INTEGER, $templateDeclarations[0]->getBaseType());
	    $this->assertEquals(Cardinality::SINGLE, $templateDeclarations[0]->getCardinality());
	    $this->assertEquals('X', $templateDeclarations[0]->getIdentifier());
	    $this->assertEquals(BaseType::INTEGER, $templateDeclarations[1]->getBaseType());
	    $this->assertEquals(Cardinality::SINGLE, $templateDeclarations[1]->getCardinality());
	    $this->assertEquals('Y', $templateDeclarations[1]->getIdentifier());
	    
	    // customInteraction
	    $customInteractions = $item->getComponentsByClassName('customInteraction');
	    $this->assertEquals(1, count($customInteractions));
	    
	    $customInteraction = $customInteractions[0];
	    $this->assertEquals('RESPONSE', $customInteraction->getResponseIdentifier());
	    $this->assertEquals('graph1', $customInteraction->getId());
	    
	    // xml content
	    $customInteractionElt = $customInteraction->getXml()->documentElement;
	    $this->assertEquals('RESPONSE', $customInteractionElt->getAttribute('responseIdentifier'));
	    $this->assertEquals('graph1', $customInteractionElt->getAttribute('id'));
	    
	   
	    $pci = 'http://www.imsglobal.org/xsd/portableCustomInteraction';
	    // -- pci:portableCustomInteraction
	    $portableCustomInteractionElts = $customInteractionElt->getElementsByTagNameNS($pci, 'portableCustomInteraction');
	    $this->assertEquals(1, $portableCustomInteractionElts->length);
	    $this->assertEquals('IW30MX6U48JF9120GJS', $portableCustomInteractionElts->item(0)->getAttribute('customInteractionTypeIdentifier'));
	    
	    // --pci:templateVariableMapping
	    $templateVariableMappingElts = $customInteractionElt->getElementsByTagNameNS($pci, 'templateVariableMapping');
	    $this->assertEquals(2, $templateVariableMappingElts->length);
	    $this->assertEquals('X', $templateVariableMappingElts->item(0)->getAttribute('templateIdentifier'));
	    $this->assertEquals('areaX', $templateVariableMappingElts->item(0)->getAttribute('configurationProperty'));
	    $this->assertEquals('Y', $templateVariableMappingElts->item(1)->getAttribute('templateIdentifier'));
	    $this->assertEquals('areaY', $templateVariableMappingElts->item(1)->getAttribute('configurationProperty'));
	    
	    // --pci:instance
	    $instanceElts = $customInteractionElt->getElementsByTagNameNS($pci, 'instance');
	    $this->assertEquals(1, $instanceElts->length);
	    
	    // --xhtml:script
	    $xhtml = 'http://www.w3.org/1999/xhtml';
	    $scriptElts = $instanceElts->item(0)->getElementsByTagNameNS($xhtml, 'script');
	    $this->assertEquals(2, $scriptElts->length);
	    $this->assertEquals('text/javascript', $scriptElts->item(0)->getAttribute('type'));
	    $this->assertEquals('js/graph.js', $scriptElts->item(0)->getAttribute('src'));
	    $this->assertEquals('text/javascript', $scriptElts->item(1)->getAttribute('type'));
	    $this->assertEquals(7, mb_strpos($scriptElts->item(1)->nodeValue, 'qtiCustomInteractionContext.setConfiguration(', 0, 'UTF-8'));
	    
	    // --xhtml:div
	    $divElts = $instanceElts->item(0)->getElementsByTagNameNS($xhtml, 'div');
	    $this->assertEquals(1, $divElts->length);
	    $this->assertEquals('graph1_box', $divElts->item(0)->getAttribute('id'));
	    $this->assertEquals('graph', $divElts->item(0)->getAttribute('class'));
	    $this->assertEquals('width:500px; height:500px;', $divElts->item(0)->getAttribute('style'));
	}
	
	public function testWritePCIItem() {
	    $doc = new XmlDocument();
	    $doc->load(self::samplesDir() . 'custom/interactions/custom_interaction_pci.xml');
	    
	    $file = tempnam('/tmp', 'qsm');
	    $doc->save($file);
	    
	    $this->testLoadPCIItem($file);
	    unlink($file);
	}
	
	public function testLoadPICItem() {
	    $doc = new XmlDocument();
	    $doc->load(self::samplesDir() . 'custom/pic.xml');
	    $this->assertTrue(true);
	}
	
	public function validFileProvider() {
		return array(
		    array(self::decorateUri('adaptive.xml')),
		    array(self::decorateUri('adaptive_template.xml')),
		    array(self::decorateUri('mc_stat2.xml')),
		    array(self::decorateUri('mc_calc3.xml')),
		    array(self::decorateUri('mc_calc5.xml')),
			array(self::decorateUri('associate.xml')),
			array(self::decorateUri('choice_fixed.xml')),
			// @todo C10 is invalid identifier? Double check! (Actually it seems the example is fucked up... we'll see).
			//array(self::decorateUri('choice_multiple_chocolade.xml')),
		    array(self::decorateUri('modalFeedback.xml')),
		    array(self::decorateUri('feedbackInline.xml')),
			array(self::decorateUri('choice_multiple.xml')),
			array(self::decorateUri('choice.xml')),
			array(self::decorateUri('extended_text_rubric.xml')),
			array(self::decorateUri('extended_text.xml')),
			array(self::decorateUri('gap_match.xml')),
			array(self::decorateUri('graphic_associate.xml')),
			array(self::decorateUri('graphic_gap_match.xml')),
			array(self::decorateUri('hotspot.xml')),
			array(self::decorateUri('hottext.xml')),
			array(self::decorateUri('inline_choice.xml')),
			array(self::decorateUri('match.xml')),
			array(self::decorateUri('multi-input.xml')),
			array(self::decorateUri('order.xml')),
			array(self::decorateUri('position_object.xml')),
			array(self::decorateUri('select_point.xml')),
			array(self::decorateUri('slider.xml')),
			array(self::decorateUri('text_entry.xml')),
		    array(self::decorateUri('template.xml')),
		    array(self::decorateUri('math.xml')),
		    array(self::decorateUri('feedbackblock_solution_random.xml')),
		    array(self::decorateUri('feedbackblock_adaptive.xml')),
		    array(self::decorateUri('orkney1.xml')),
		    array(self::decorateUri('orkney2.xml')),
		    array(self::decorateUri('nested_object.xml')),
		    array(self::decorateUri('likert.xml')),
		    //array(self::decorateUri('feedbackblock_templateblock.xml')),
			array(self::decorateUri('associate.xml', '2.0')),
		    array(self::decorateUri('associate_lang.xml', '2.0')),
			array(self::decorateUri('adaptive.xml', '2.0')),
			array(self::decorateUri('choice_multiple.xml', '2.0')),
			array(self::decorateUri('choice.xml', '2.0')),
			array(self::decorateUri('drawing.xml', '2.0')),
			array(self::decorateUri('extended_text.xml', '2.0')),
			array(self::decorateUri('feedback.xml', '2.0')),
			array(self::decorateUri('gap_match.xml', '2.0')),
			array(self::decorateUri('graphic_associate.xml', '2.0')),
			array(self::decorateUri('graphic_gap_match.xml', '2.0')),
			array(self::decorateUri('graphic_order.xml', '2.0')),
			array(self::decorateUri('hint.xml', '2.0')),
			array(self::decorateUri('hotspot.xml', '2.0')),
			//array(self::decorateUri('hottext.xml', '2.0')),
			array(self::decorateUri('inline_choice.xml', '2.0')),
			array(self::decorateUri('likert.xml', '2.0')),
			array(self::decorateUri('match.xml', '2.0')),
			array(self::decorateUri('nested_object.xml', '2.0')),
			array(self::decorateUri('order_partial_scoring.xml', '2.0')),
			array(self::decorateUri('order.xml', '2.0')),
			array(self::decorateUri('orkney1.xml', '2.0')),
			//array(self::decorateUri('position_object.xml', '2.0')),
			array(self::decorateUri('select_point.xml', '2.0')),
			array(self::decorateUri('slider.xml', '2.0')),
			array(self::decorateUri('template_image.xml', '2.0')),
			array(self::decorateUri('template.xml', '2.0')),
			array(self::decorateUri('text_entry.xml', '2.0')),
			array(self::decorateUri('upload_composite.xml', '2.0')),
			array(self::decorateUri('upload.xml', '2.0')),
		    
		    // Other miscellaneous items...
		    array(self::samplesDir() . 'custom/items/custom_operator_item.xml'),
		);
	}
	
	private static function decorateUri($uri, $version = '2.1') {
		if ($version === '2.1') {
			return self::samplesDir() . 'ims/items/2_1/' . $uri;
		}
		else {
			return self::samplesDir() . 'ims/items/2_0/' . $uri;
		}
	}
}
