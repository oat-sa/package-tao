<?php

use qtism\data\SubmissionMode;
use qtism\data\NavigationMode;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\data\storage\xml\XmlDocument;
use qtism\data\storage\xml\XmlStorageException;
use qtism\data\AssessmentTest;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class XmlAssessmentDocumentQTIGuideTest extends QtiSmTestCase {
	
	/**
	 * @dataProvider qtiImplementationGuideAssessmentTestFiles
	 * 
	 * @param string $uri The URI describing the file to load.
	 */
	public function testLoadNoSchemaValidate($uri) {
		$doc = new XmlDocument('2.1');
		$doc->load($uri);
		$this->assertInstanceOf('qtism\\data\\storage\\xml\\XmlDocument', $doc);
		$this->assertInstanceOf('qtism\\data\\AssessmentTest', $doc->getDocumentComponent());
	}
	
	/**
	 * @dataProvider qtiImplementationGuideAssessmentTestFiles
	 *
	 * @param string $uri The URI describing the file to load.
	 */
	public function testLoadFromStringNoSchemaValidate($uri) {
	    $doc = new XmlDocument('2.1');
	    $doc->loadFromString(file_get_contents($uri));
	    $this->assertInstanceOf('qtism\\data\\storage\\xml\\XmlDocument', $doc);
	    $this->assertInstanceOf('qtism\\data\\AssessmentTest', $doc->getDocumentComponent());
	}
	
	/**
	 * @dataProvider qtiImplementationGuideAssessmentTestFiles
	 * 
	 * @param string $uri The URI describing the file to load.
	 */
	public function testLoadSaveSchemaValidate($uri) {
		$doc = new XmlDocument('2.1');
		$doc->load($uri);
		
		$file = tempnam('/tmp', 'qsm');
		$doc->save($file);
		
		$doc = new XmlDocument('2.1');
		try {
			$doc->load($file, true); // validate on load.
			$this->assertTrue(true);
			unlink($file);
		}
		catch (XmlStorageException $e) {
			$this->assertTrue(false, $e->getMessage());
			unlink($file);
		}
	}
	
	/**
	 * @dataProvider qtiImplementationGuideAssessmentTestFiles
	 *
	 * @param string $uri The URI describing the file to load.
	 */
	public function testLoadSaveToStringSchemaValidate($uri) {
	    $doc = new XmlDocument('2.1');
	    $doc->load($uri);
	
	    $file = tempnam('/tmp', 'qsm');
	    $str = $doc->saveToString();
	    file_put_contents($file, $str);
	
	    $doc = new XmlDocument('2.1');
	    try {
	        $doc->load($file, true); // validate on load.
	        $this->assertTrue(true);
	        unlink($file);
	    }
	    catch (XmlStorageException $e) {
	        $this->assertTrue(false, $e->getMessage());
	        unlink($file);
	    }
	}
	
	public function qtiImplementationGuideAssessmentTestFiles() {
		return array(
			array(self::decorateUri('interaction_mix_sachsen/interaction_mix_sachsen.xml')),
			array(self::decorateUri('simple_feedback_test/simple_feedback_test.xml')),
			array(self::decorateUri('feedback_examples_test/feedback_examples_test.xml')),
			array(self::decorateUri('sets_of_items_with_leading_material/sets_of_items_with_leading_material.xml')),
			array(self::decorateUri('arbitrary_collections_of_item_outcomes/arbitrary_collections_of_item_outcomes.xml')),
			array(self::decorateUri('categories_of_item/categories_of_item.xml')),
			array(self::decorateUri('arbitrary_weighting_of_item_outcomes/arbitrary_weighting_of_item_outcomes.xml')),
			array(self::decorateUri('specifiying_the_number_of_allowed_attempts/specifiying_the_number_of_allowed_attempts.xml')),
			array(self::decorateUri('controlling_item_feedback_in_relation_to_the_test/controlling_item_feedback_in_relation_to_the_test.xml')),
			array(self::decorateUri('controlling_the_duration_of_an_item_attempt/controlling_the_duration_of_an_item_attempt.xml')),
			array(self::decorateUri('early_termination_of_test_based_on_accumulated_item_outcomes/early_termination_of_test_based_on_accumulated_item_outcomes.xml')),
			array(self::decorateUri('golden_required_items_and_sections/golden_required_items_and_sections.xml')),
			array(self::decorateUri('branching_based_on_the_response_to_an_assessmentitem/branching_based_on_the_response_to_an_assessmentitem.xml')),
			array(self::decorateUri('items_arranged_into_sections_within_tests/items_arranged_into_sections_within_tests.xml')),
			array(self::decorateUri('randomizing_the_order_of_items_and_sections/randomizing_the_order_of_items_and_sections.xml')),
			array(self::decorateUri('basic_statistics_as_outcomes/basic_statistics_as_outcomes.xml')),
			array(self::decorateUri('mapping_item_outcomes_prior_to_aggregation/mapping_item_outcomes_prior_to_aggregation.xml'))
		);
	}
	
	public function testLoadInteractionMixSachsen($assessmentTest = null) {
		
		if (empty($assessmentTest)) {
			$doc = new XmlDocument('2.1');
			$doc->load(self::decorateUri('interaction_mix_sachsen/interaction_mix_sachsen.xml'));
			$assessmentTest = $doc;
		}

		$assessmentTest->schemaValidate();
		
		$this->assertInstanceOf('qtism\\data\\AssessmentTest', $assessmentTest->getDocumentComponent());
		$this->assertEquals('InteractionMixSachsen_1901710679', $assessmentTest->getDocumentComponent()->getIdentifier());
		$this->assertEquals('Interaction Mix (Sachsen)', $assessmentTest->getDocumentComponent()->getTitle());
		
		// -- OutcomeDeclarations
		$outcomeDeclarations = $assessmentTest->getDocumentComponent()->getOutcomeDeclarations();
		$this->assertEquals(2, count($outcomeDeclarations));
		
		$outcomeDeclaration = $outcomeDeclarations['SCORE'];
		$this->assertEquals('SCORE', $outcomeDeclaration->getIdentifier());
		$this->assertEquals(Cardinality::SINGLE, $outcomeDeclaration->getCardinality());
		$this->assertEquals(BaseType::FLOAT, $outcomeDeclaration->getBaseType());
		$defaultValue = $outcomeDeclaration->getDefaultValue();
		$this->assertInstanceOf('qtism\\data\\state\\DefaultValue', $defaultValue);
		$values = $defaultValue->getValues();
		$this->assertInstanceOf('qtism\\data\\state\\ValueCollection', $values);
		$this->assertEquals(1, count($values));
		$value = $values[0];
		$this->assertInstanceOf('qtism\\data\\state\\Value', $value);
		$this->assertInternalType('float', $value->getValue());
		$this->assertEquals(0.0, $value->getValue());
		
		$outcomeDeclaration = $outcomeDeclarations['MAXSCORE'];
		$this->assertEquals('MAXSCORE', $outcomeDeclaration->getIdentifier());
		$this->assertEquals(Cardinality::SINGLE, $outcomeDeclaration->getCardinality());
		$this->assertEquals(BaseType::FLOAT, $outcomeDeclaration->getBaseType());
		$defaultValue = $outcomeDeclaration->getDefaultValue();
		$this->assertInstanceOf('qtism\\data\\state\\DefaultValue', $defaultValue);
		$values = $defaultValue->getValues();
		$this->assertInstanceOf('qtism\\data\\state\\ValueCollection', $values);
		$this->assertEquals(1, count($values));
		$value = $values[0];
		$this->assertInstanceOf('qtism\\data\\state\\Value', $value);
		$this->assertInternalType('float', $value->getValue());
		$this->assertEquals(18.0, $value->getValue());
		
		// -- TestParts
		$testParts = $assessmentTest->getDocumentComponent()->getTestParts();
		$this->assertEquals(1, count($testParts));
		$testPart = $testParts['testpartID'];
		$this->assertInstanceOf('qtism\\data\\TestPart', $testPart);
		$this->assertEquals('testpartID', $testPart->getIdentifier());
		$this->assertEquals(NavigationMode::NONLINEAR, $testPart->getNavigationMode());
		$this->assertEquals(SubmissionMode::INDIVIDUAL, $testPart->getSubmissionMode());
		
		// -- AssessmentSections
		$assessmentSections = $testPart->getAssessmentSections();
		$this->assertEquals(1, count($assessmentSections));
		$assessmentSection = $assessmentSections['Sektion_181865064'];
		$this->assertInstanceOf('qtism\\data\\AssessmentSection', $assessmentSection);
		$this->assertEquals('Sektion_181865064', $assessmentSection->getIdentifier());
		$this->assertFalse($assessmentSection->isFixed());
		$this->assertFalse($assessmentSection->isVisible());
		$this->assertEquals('Sektion', $assessmentSection->getTitle());
		
		// -- AssessmentItemRefs
		$assessmentItemRefs = $assessmentSection->getSectionParts();
		$this->assertInstanceOf('qtism\\data\\SectionPartCollection', $assessmentItemRefs);
		$this->assertEquals(13, count($assessmentItemRefs));
		
		$expectedItems = array(
			array('Choicetruefalse_176040516', "Choicetruefalse_176040516.xml"),
			array('Choicesingle_853928446', 'Choicesingle_853928446.xml'),
			array('Choicemultiple_2014410822', 'Choicemultiple_2014410822.xml'),
			array('Choicemultiple_871212949', 'Choicemultiple_871212949.xml'),
			array('Hotspot_278940407', 'Hotspot_278940407.xml'),
			array('Order_913967682', 'Order_913967682.xml'),
			array('Matchsingle_143114773', 'Matchsingle_143114773.xml'),
			array('Matchmultiple_1038910213', 'Matchmultiple_1038910213.xml'),
			array('TextEntry_883368511', 'TextEntry_883368511.xml'),
			array('TextEntrynumeric_2040297025', 'TextEntrynumeric_2040297025.xml'),
			array('TextEntrynumeric_770468849', 'TextEntrynumeric_770468849.xml'),
			array('TextEntrysubset_806481421', 'TextEntrysubset_806481421.xml'),
			array('Hottext_801974120', 'Hottext_801974120.xml')
		);
		
		for ($i = 0; $i < count($assessmentItemRefs); $i++) {
			$id = $expectedItems[$i][0];
			$file = $expectedItems[$i][1];
			
			$this->assertInstanceOf('qtism\\data\\AssessmentItemRef', $assessmentItemRefs[$id]);
			$this->assertEquals($id, $assessmentItemRefs[$id]->getIdentifier());
			$this->assertEquals($file, $assessmentItemRefs[$id]->getHref());
			$this->assertFalse($assessmentItemRefs[$id]->isFixed());
		}
		
		// OutcomeProcessing
		$outcomeProcessing = $assessmentTest->getDocumentComponent()->getOutcomeProcessing();
		$this->assertInstanceOf('qtism\\data\\processing\\OutcomeProcessing', $outcomeProcessing);
		$this->assertEquals(1, count($outcomeProcessing->getOutcomeRules()));
		
		$outcomeRules = $outcomeProcessing->getOutcomeRules();
		$setOutcomeValue = $outcomeRules[0];;
		$this->assertInstanceOf('qtism\\data\\rules\\SetOutcomeValue', $setOutcomeValue);
		$this->assertEquals('SCORE', $setOutcomeValue->getIdentifier());
		$sum = $setOutcomeValue->getExpression();
		$this->assertInstanceOf('qtism\\data\\expressions\\operators\\Sum', $sum);
		
		$expressions = $sum->getExpressions();
		$testVariables = $expressions[0];
		$this->assertInstanceOf('qtism\\data\\expressions\\TestVariables', $testVariables);
		$this->assertEquals('SCORE', $testVariables->getVariableIdentifier());
	}
	
	public function testWriteInteractionMixSachsen() {
		$doc = new XmlDocument('2.1');
		$doc->load(self::decorateUri('interaction_mix_sachsen/interaction_mix_sachsen.xml'), true);
		
		$file = tempnam('/tmp', 'qsm');
		$doc->save($file);
		$this->assertTrue(file_exists($file));
		
		$doc = new XmlDocument('2.1');
		$doc->load($file);
		$this->testLoadInteractionMixSachsen($doc);
		
		// correctly namespaced ?
		$dom = $doc->getDomDocument();
		$assessmentTestElt = $dom->documentElement;
		$this->assertEquals('assessmentTest', $assessmentTestElt->nodeName);
		$this->assertEquals('http://www.imsglobal.org/xsd/imsqti_v2p1', $assessmentTestElt->namespaceURI);
		
		// childrend in namespace ?
		$outcomeDeclarationElts = $dom->documentElement->getElementsByTagName('outcomeDeclaration');
		$this->assertEquals(2, $outcomeDeclarationElts->length);
		$outcomeDeclarationElt = $outcomeDeclarationElts->item(0);
		$this->assertEquals('http://www.imsglobal.org/xsd/imsqti_v2p1', $outcomeDeclarationElt->namespaceURI);
		
		unlink($file);
		$this->assertFalse(file_exists($file));
	}
	
	private static function decorateUri($uri) {
		return self::samplesDir() . 'ims/tests/' . $uri;
	}
}