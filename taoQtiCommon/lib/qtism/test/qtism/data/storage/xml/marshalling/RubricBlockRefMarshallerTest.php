<?php

use qtism\data\content\RubricBlockRef;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class RubricBlockRefMarshallerTest extends QtiSmTestCase {
    
    public function testUnmarshall() {
        $ref = $this->createComponentFromXml('<rubricBlockRef identifier="R01" href="./R01.xml"/>');
        $this->assertEquals('R01', $ref->getIdentifier());
        $this->assertEquals('./R01.xml', $ref->getHref());
    }

    public function testMarshall() {
        $ref = new RubricBlockRef('R01', './R01.xml');
        $marshaller = $this->getMarshallerFactory()->createMarshaller($ref);
        $elt = $marshaller->marshall($ref);
        
        $this->assertEquals('rubricBlockRef', $elt->nodeName);
        $this->assertEquals('R01', $elt->getAttribute('identifier'));
        $this->assertEquals('./R01.xml', $elt->getAttribute('href'));
    }
}