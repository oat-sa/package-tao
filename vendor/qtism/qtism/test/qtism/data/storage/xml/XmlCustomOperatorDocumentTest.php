<?php

use qtism\common\enums\BaseType;
use qtism\data\storage\xml\XmlDocument;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class XmlCustomOperatorDocumentTest extends QtiSmTestCase {
	
    public function testReadNoLax($url = '') {
        $doc = new XmlDocument();
        $url = (empty($url) === true) ? (self::samplesDir() . 'custom/operators/custom_operator_1.xml') : $url;
        $doc->load($url, true);
        $customOperator = $doc->getDocumentComponent();
        
        $this->assertInstanceOf('qtism\\data\\expressions\\operators\\CustomOperator', $customOperator);
        $this->assertEquals('com.taotesting.qtism.customOperator1', $customOperator->getClass());
        $this->assertEquals('http://qtism.taotesting.com/xsd/customOperator1.xsd', $customOperator->getDefinition());
        
        $xml = $customOperator->getXml();
        $this->assertEquals('false', $xml->documentElement->getAttributeNS('http://qtism.taotesting.com', 'debug'));
        $this->assertEquals('default', $xml->documentElement->getAttributeNS('http://qtism.taotesting.com', 'syntax'));
        
        $expressions = $customOperator->getExpressions();
        $this->assertEquals(1, count($expressions));
        $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $expressions[0]);
        $this->assertEquals(BaseType::STRING, $expressions[0]->getBaseType());
        $this->assertEquals('Param1Data', $expressions[0]->getValue());
    }
    
    public function testWriteNoLax() {
        $doc = new XmlDocument();
        $doc->load(self::samplesDir() . 'custom/operators/custom_operator_1.xml');
        
        $file = tempnam('/tmp', 'qsm');
        $doc->save($file);
        $this->testReadNoLax($file);
        
        unlink($file);
    }
    
    public function testReadQTIOnly($url = '') {
        $doc = new XmlDocument();
        $url = (empty($url) === true) ? (self::samplesDir() . 'custom/operators/custom_operator_2.xml') : $url;
        $doc->load($url, true);
        $customOperator = $doc->getDocumentComponent();
        
        $this->assertInstanceOf('qtism\\data\\expressions\\operators\\CustomOperator', $customOperator);
        
        $expressions = $customOperator->getExpressions();
        $this->assertEquals(1, count($expressions));
        $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $expressions[0]);
        $this->assertEquals(BaseType::STRING, $expressions[0]->getBaseType());
        $this->assertEquals('Param1Data', $expressions[0]->getValue());
    }
    
    public function testWriteQTIOnly() {
        $doc = new XmlDocument();
        $doc->load(self::samplesDir() . 'custom/operators/custom_operator_2.xml');
        
        $file = tempnam('/tmp', 'qsm');
        $doc->save($file);
        $this->testReadQTIOnly($file);
        
        unlink($file);
    }
    
    public function testReadFullLax($url = '') {
        $doc = new XmlDocument();
        $url = (empty($url) === true) ? (self::samplesDir() . 'custom/operators/custom_operator_3.xml') : $url;
        $doc->load($url, true);
        $customOperator = $doc->getDocumentComponent();
        
        $this->assertInstanceOf('qtism\\data\\expressions\\operators\\CustomOperator', $customOperator);
        $this->assertEquals('com.taotesting.qtism.customOperator1', $customOperator->getClass());
        $this->assertEquals('http://qtism.taotesting.com/xsd/customOperator1.xsd', $customOperator->getDefinition());
        
        $xml = $customOperator->getXml();
        $this->assertEquals('false', $xml->documentElement->getAttributeNS('http://qtism.taotesting.com', 'debug'));
        $this->assertEquals('default', $xml->documentElement->getAttributeNS('http://qtism.taotesting.com', 'syntax'));
        
        $expressions = $customOperator->getExpressions();
        $this->assertEquals(1, count($expressions));
        $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $expressions[0]);
        $this->assertEquals(BaseType::STRING, $expressions[0]->getBaseType());
        $this->assertEquals('Param1Data', $expressions[0]->getValue());
        
        // Now check the LAX content.
        $laxNodes = $xml->getElementsByTagNameNS('http://qtism.taotesting.com', 'config');
        $this->assertEquals(1, $laxNodes->length);
        $this->assertEquals('qtism', $laxNodes->item(0)->prefix);
        
        $logNodes = $laxNodes->item(0)->getElementsByTagNameNS('http://qtism.taotesting.com', 'log');
        $this->assertEquals(1, $logNodes->length);
        $this->assertEquals('true', $logNodes->item(0)->nodeValue);
        
        $repoNodes = $laxNodes->item(0)->getElementsByTagNameNS('http://qtism.taotesting.com', 'repository');
        $this->assertEquals(1, $repoNodes->length);
        $this->assertEquals('./repo', $repoNodes->item(0)->nodeValue);
    }
    
    public function testWriteFullLax($url = '') {
        $doc = new XmlDocument();
        $doc->load(self::samplesDir() . 'custom/operators/custom_operator_3.xml');
        
        $file = tempnam('/tmp', 'qsm');
        $doc->save($file);
        
        $this->testReadFullLax($file);
        unlink($file);
    }
    
    public function testReadNestedLax($url = '') {
        $doc = new XmlDocument();
        $url = (empty($url) === true) ? (self::samplesDir() . 'custom/operators/custom_operator_nested_1.xml') : $url;
        $doc->load($url, true);
        $customOperator = $doc->getDocumentComponent();
        
        $this->assertInstanceOf('qtism\\data\\expressions\\operators\\CustomOperator', $customOperator);
        $this->assertEquals('com.taotesting.qtism.customOperator', $customOperator->getClass());
        $this->assertEquals('http://qtism.taotesting.com/xsd/nestedOperator.xsd', $customOperator->getDefinition());
        
        // Check LAX attributes.
        $this->assertEquals('false', $customOperator->getXml()->documentElement->getAttributeNS('http://qtism.taotesting.com', 'debug'));
        $this->assertEquals('default', $customOperator->getXml()->documentElement->getAttributeNS('http://qtism.taotesting.com', 'syntax'));
        
        // Check LAX content.
        $configElts = $customOperator->getXml()->documentElement->getElementsByTagNameNS('http://qtism.taotesting.com', 'config');
        $this->assertEquals(1, $configElts->length);
        
        $logElts = $customOperator->getXml()->documentElement->getElementsByTagNameNS('http://qtism.taotesting.com', 'log');
        $this->assertEquals(1, $logElts->length);
        $this->assertEquals('true', $logElts->item(0)->nodeValue);
        
        $repoElts = $customOperator->getXml()->documentElement->getElementsByTagNameNS('http://qtism.taotesting.com', 'repository');
        $this->assertEquals(1, $repoElts->length);
        $this->assertEquals('./repo', $repoElts->item(0)->nodeValue);
        
        // Contains two sub expressions (customOperator, baseValue).
        $expressions = $customOperator->getExpressions();
        $this->assertEquals(2, count($expressions));
        
        // -- Checking customOperator (first child of customOperator).
        $customOperator = $expressions[0];
        $this->assertInstanceOf('qtism\\data\\expressions\\operators\\CustomOperator', $customOperator);
        $this->assertEquals('com.taotesting.nestedCustomOperator', $customOperator->getClass());
        $this->assertFalse($customOperator->hasDefinition());
        $subExpressions = $customOperator->getExpressions();
        $this->assertEquals(1, count($subExpressions));
        $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $subExpressions[0]);
        $this->assertEquals(BaseType::STRING, $subExpressions[0]->getBaseType());
        $this->assertEquals('Some data to pass to nestedCustomOperator...', $subExpressions[0]->getValue());
        // Check LAX content.
        $spoElts = $customOperator->getXml()->documentElement->getElementsByTagNameNS('http://subject.predicate.object.com', 'stmt');
        $this->assertEquals(1, $spoElts->length);
        $this->assertEquals('spo', $spoElts->item(0)->nodeValue);
        
        // -- Checking baseValue (second child of customOperator).
        $baseValue = $expressions[1];
        $this->assertInstanceOf('qtism\\data\\expressions\\BaseValue', $baseValue);
        $this->assertEquals(BaseType::STRING, $baseValue->getBaseType());
        $this->assertEquals('Param1Data', $baseValue->getValue());
    }
    
    public function testWriteNestedLax() {
        $doc = new XmlDocument();
        $doc->load(self::samplesDir() . 'custom/operators/custom_operator_nested_1.xml');
        
        $file = tempnam('/tmp', 'qsm');
        $doc->save($file);
        
        $this->testReadNestedLax($file);
        //unlink($file);
    }
}