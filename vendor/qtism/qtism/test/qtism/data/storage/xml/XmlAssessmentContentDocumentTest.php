<?php

use qtism\data\storage\xml\XmlDocument;

require_once (dirname(__FILE__) . '/../../../../QtiSmTestCase.php');

class XmlAssessmentContentDocumentTest extends QtiSmTestCase {
	
    public function testSimpleXmlBase() {
        $doc = new XmlDocument();
        $doc->load(self::samplesDir() . 'rendering/xmlbase_1.xml');
        
        $div = $doc->getDocumentComponent();
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\Div', $div);
        $this->assertTrue($div->hasXmlBase());
        $this->assertEquals('http://www.qtism-project.org/', $div->getXmlBase());
        
        $imgs = $div->getComponentsByClassName('img');
        $this->assertEquals(3, count($imgs));
        
        $this->assertFalse($imgs[0]->hasXmlBase());
        $this->assertFalse($imgs[1]->hasXmlBase());
        $this->assertFalse($imgs[2]->hasXmlBase());
    }
    
    public function testModerateXmlBase() {
        $doc = new XmlDocument();
        $doc->load(self::samplesDir() . 'rendering/xmlbase_2.xml');
        
        $div = $doc->getDocumentComponent();
        $this->assertInstanceOf('qtism\\data\\content\\xhtml\\text\\Div', $div);
        $this->assertFalse($div->hasXmlBase());
        $this->assertEquals('', $div->getXmlBase());
        
        $subDivs = $div->getComponentsByClassName('div');
        $this->assertEquals(2, count($subDivs));
        
        $this->assertTrue($subDivs[0]->hasXmlBase());
        $this->assertEquals('http://www.qtism-project.org/farm/', $subDivs[0]->getXmlBase());
        $this->assertTrue($subDivs[1]->hasXmlBase());
        $this->assertEquals('http://www.qtism-project.org/birds/', $subDivs[1]->getXmlBase());
    }
}