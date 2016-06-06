<?php

use qtism\data\storage\xml\XmlCompactDocument;
use qtism\data\storage\php\PhpDocument;
use qtism\data\storage\xml\XmlDocument;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class PhpDocumentTest extends QtiSmTestCase {
	
    public function testSimpleLoad() {
        
        $doc = new PhpDocument();
        $doc->load(self::samplesDir() . 'custom/php/php_storage_simple.php');
        
        $assessmentTest = $doc->getDocumentComponent();
        $this->assertInstanceOf('qtism\\data\\AssessmentTest', $assessmentTest);
        
        $this->assertEquals('php_storage_simple', $assessmentTest->getIdentifier());
    }
    
    public function testSimpleLoadFromString() {
        $doc = new PhpDocument();
        $doc->loadFromString(file_get_contents(self::samplesDir() . 'custom/php/php_storage_simple.php'));
        
        $assessmentTest = $doc->getDocumentComponent();
        $this->assertInstanceOf('qtism\\data\\AssessmentTest', $assessmentTest);
        
        $this->assertEquals('php_storage_simple', $assessmentTest->getIdentifier());
    }
    
     public function testSimpleSave() {

        $doc = new XmlCompactDocument();
        $doc->load(self::samplesDir() . 'custom/php/php_storage_simple.xml');
        $phpDoc = new PhpDocument('2.1', $doc->getDocumentComponent());
        $file = tempnam('/tmp', 'qsm');
        $phpDoc->save($file);
        
        unlink($file);
    }
    
    public function testSimpleSaveToString() {
        $doc = new XmlCompactDocument();
        $doc->load(self::samplesDir() . 'custom/php/php_storage_simple.xml');
        $phpDoc = new PhpDocument('2.1', $doc->getDocumentComponent());
        $phpStr = $phpDoc->saveToString();
        
        $phpDoc->loadFromString($phpStr);
        $this->assertEquals('php_storage_simple', $phpDoc->getDocumentComponent()->getIdentifier());
    }
    
    public function testCustomOperatorOne() {
        $doc = new XmlDocument();
        $doc->load(self::samplesDir() . 'custom/operators/custom_operator_1.xml');
        $phpDoc = new PhpDocument('2.1', $doc->getDocumentComponent());
        
        $file = tempnam('/tmp', 'qsm');
        $phpDoc->save($file);
        
        $phpDoc = new PhpDocument();
        $phpDoc->load($file);
        
        $customOperator = $phpDoc->getDocumentComponent();
        $xml = $customOperator->getXml();
        $this->assertInstanceOf('qtism\\data\\expressions\\operators\\CustomOperator', $customOperator);
        $this->assertEquals('com.taotesting.qtism.customOperator1', $customOperator->getClass());
        $this->assertEquals('http://qtism.taotesting.com/xsd/customOperator1.xsd', $customOperator->getDefinition());
        $this->assertEquals('false', $xml->documentElement->getAttributeNS('http://qtism.taotesting.com', 'debug'));
        $this->assertEquals('default', $xml->documentElement->getAttributeNS('http://qtism.taotesting.com', 'syntax'));
        $this->assertEquals('<customOperator xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:qtism="http://qtism.taotesting.com" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1 http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd" class="com.taotesting.qtism.customOperator1" definition="http://qtism.taotesting.com/xsd/customOperator1.xsd" qtism:debug="false" qtism:syntax="default"><baseValue baseType="string"><![CDATA[Param1Data]]></baseValue></customOperator>', $xml->saveXML($xml->documentElement));
        
        unlink($file);
    }
    
    public function testCustomOperatorTwo() {
        $doc = new XmlDocument();
        $doc->load(self::samplesDir() . 'custom/operators/custom_operator_2.xml');
        $phpDoc = new PhpDocument('2.1', $doc->getDocumentComponent());
        
        $file = tempnam('/tmp', 'qsm');
        $phpDoc->save($file);
        
        $phpDoc = new PhpDocument();
        $phpDoc->load($file);
        
        $customOperator = $phpDoc->getDocumentComponent();
        $xml = $customOperator->getXml();
        $this->assertInstanceOf('qtism\\data\\expressions\\operators\\CustomOperator', $customOperator);
        $this->assertEquals('<customOperator xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1 http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd"><baseValue baseType="string"><![CDATA[Param1Data]]></baseValue></customOperator>', $xml->saveXML($xml->documentElement));
        
        unlink($file);
    }
    
    /**
     *
     * @dataProvider loadTestSamplesDataProvider
     * @param string $testUri
     * @param string $rootType The expected fully qualified class name of the document component.
     */
    public function testLoadTestSamples($testUri, $rootType) {
        // Basic XML -> PHP transormation + save + load
        $xmlDoc = new XmlDocument('2.1');
        $xmlDoc->load($testUri);
    
        $phpDoc = new PhpDocument();
        $phpDoc->setDocumentComponent($xmlDoc->getDocumentComponent());
    
        $file = tempnam('/tmp', 'qsm');
        $phpDoc->save($file);
    
        $phpDoc = new PhpDocument();
        $phpDoc->load($file);
    
        $this->assertInstanceOf($rootType, $phpDoc->getDocumentComponent());
        $this->assertEquals($file, $phpDoc->getUrl());
    
        unlink($file);
        $this->assertFalse(file_exists($file));
    }
    
    /**
     *
     * @dataProvider loadTestSamplesDataProvider
     * @param string $testUri
     * @param string $rootType The expected fully qualified class name of the document component.
     */
    public function testLoadTestSamplesFromString($testUri, $rootType) {
        // Basic XML -> PHP transormation + saveTotring + loadFromString
        $xmlDoc = new XmlDocument('2.1');
        $xmlDoc->loadFromString(file_get_contents($testUri));
    
        $phpDoc = new PhpDocument();
        $phpDoc->setDocumentComponent($xmlDoc->getDocumentComponent());
    
        $file = tempnam('/tmp', 'qsm');
        file_put_contents($file, $phpDoc->saveToString());
    
        $phpDoc = new PhpDocument();
        $phpDoc->loadFromString(file_get_contents($file));
    
        $this->assertInstanceOf($rootType, $phpDoc->getDocumentComponent());
        $this->assertNull($phpDoc->getUrl());
    
        unlink($file);
        $this->assertFalse(file_exists($file));
    }
    
    public function testLoadInteractionMixSaschsen() {
        $xmlDoc = new XmlDocument('2.1');
        $xmlDoc->load(self::samplesDir() . 'ims/tests/interaction_mix_sachsen/interaction_mix_sachsen.xml');
    
        $phpDoc = new PhpDocument();
        $phpDoc->setDocumentComponent($xmlDoc->getDocumentComponent());
    
        $file = tempnam('/tmp', 'qsm');
        $phpDoc->save($file);
    
        $phpDoc = new PhpDocument();
        $phpDoc->load($file);
    
        $this->assertEquals('InteractionMixSachsen_1901710679', $phpDoc->getDocumentComponent()->getIdentifier());
        unlink($file);
        $this->assertFalse(file_exists($file));
    }
    
    public function loadTestSamplesDataProvider() {
        return array(
            array(self::samplesDir() . 'ims/tests/arbitrary_collections_of_item_outcomes/arbitrary_collections_of_item_outcomes.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/arbitrary_weighting_of_item_outcomes/arbitrary_weighting_of_item_outcomes.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/basic_statistics_as_outcomes/basic_statistics_as_outcomes.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/branching_based_on_the_response_to_an_assessmentitem/branching_based_on_the_response_to_an_assessmentitem.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/controlling_the_duration_of_an_item_attempt/controlling_the_duration_of_an_item_attempt.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/controlling_item_feedback_in_relation_to_the_test/controlling_item_feedback_in_relation_to_the_test.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/early_termination_of_test_based_on_accumulated_item_outcomes/early_termination_of_test_based_on_accumulated_item_outcomes.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/feedback_examples_test/feedback_examples_test.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/golden_required_items_and_sections/golden_required_items_and_sections.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/interaction_mix_sachsen/interaction_mix_sachsen.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/items_arranged_into_sections_within_tests/items_arranged_into_sections_within_tests.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/mapping_item_outcomes_prior_to_aggregation/mapping_item_outcomes_prior_to_aggregation.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/randomizing_the_order_of_items_and_sections/randomizing_the_order_of_items_and_sections.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/sets_of_items_with_leading_material/sets_of_items_with_leading_material.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/simple_feedback_test/simple_feedback_test.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'ims/tests/specifiying_the_number_of_allowed_attempts/specifiying_the_number_of_allowed_attempts.xml', 'qtism\\data\\AssessmentTest'),
            array(self::samplesDir() . 'rendering/various_content.xml', 'qtism\\data\\content\\RubricBlock'),
            array(self::samplesDir() . 'rendering/associateinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/choiceinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/choiceinteraction_2.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/drawinginteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/endattemptinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/extendedtextinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/gapmatchinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/graphicgapmatchinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/graphicorderinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/hotspotinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/hottextinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/inlinechoiceinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/itembodywithfeedback_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/matchinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/mediainteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/mediainteraction_2.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/mediainteraction_3.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/orderinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/selectpointinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/positionobjectinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/sliderinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/textentryinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/uploadinteraction_1.xml', 'qtism\\data\\content\\ItemBody'),
            array(self::samplesDir() . 'rendering/itemfeedback_1.xml', 'qtism\\data\\AssessmentItem'),
            array(self::samplesDir() . 'rendering/empty_object.xml', 'qtism\\data\\content\\xhtml\\Object'),
            array(self::samplesDir() . 'rendering/empty_rubricblock.xml', 'qtism\\data\\content\\RubricBlock'),
            array(self::samplesDir() . 'rendering/rubricblock_1.xml', 'qtism\\data\\content\\RubricBlock'),
            array(self::samplesDir() . 'rendering/rubricblock_2.xml', 'qtism\\data\\content\\RubricBlock'),
            array(self::samplesDir() . 'rendering/rubricblock_3.xml', 'qtism\\data\\content\\RubricBlock'),
            array(self::samplesDir() . 'rendering/math_1.xml', 'qtism\\data\\AssessmentItem'),
            array(self::samplesDir() . 'rendering/math_2.xml', 'qtism\\data\\AssessmentItem'),
            array(self::samplesDir() . 'rendering/math_3.xml', 'qtism\\data\\AssessmentItem'),
            array(self::samplesDir() . 'rendering/math_4.xml', 'qtism\\data\\Content\\RubricBlock'),
            array(self::samplesDir() . 'custom/operators/custom_operator_1.xml', 'qtism\\data\\expressions\\operators\\CustomOperator'),
            array(self::samplesDir() . 'custom/operators/custom_operator_2.xml', 'qtism\\data\\expressions\\operators\\CustomOperator'),
            array(self::samplesDir() . 'custom/operators/custom_operator_3.xml', 'qtism\\data\\expressions\\operators\\CustomOperator'),
            array(self::samplesDir() . 'custom/operators/custom_operator_nested_1.xml', 'qtism\\data\\expressions\\operators\\CustomOperator'),
            array(self::samplesDir() . 'custom/interactions/custom_interaction_pci.xml', 'qtism\\data\\AssessmentItem')
        );
    }
}
