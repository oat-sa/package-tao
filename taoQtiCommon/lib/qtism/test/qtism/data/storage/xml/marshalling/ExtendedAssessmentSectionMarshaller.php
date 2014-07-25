<?php

use qtism\data\content\RubricBlockRef;

use qtism\data\content\RubricBlockRefCollection;

use qtism\data\AssessmentSectionRef;

use qtism\data\SectionPartCollection;

use qtism\data\ExtendedAssessmentSection;

use qtism\data\storage\xml\marshalling\CompactMarshallerFactory;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class ExtendedAssessmentSectionMarshallerTest extends QtiSmTestCase {
    
    public function testUnmarshall() {
        $elt = $this->createDOMElement('
            <assessmentSection identifier="S01" title="Section 01" visible="true">
                <assessmentSectionRef identifier="SR01" href="./SR01.xml"/>
                <rubricBlockRef identifier="R01" href="./R01.xml"/>
            </assessmentSection>
        ');
        
        $factory = new CompactMarshallerFactory();
        $marshaller = $factory->createMarshaller($elt);
        
        $section = $marshaller->unmarshall($elt);
        $this->assertInstanceOf('qtism\\data\\ExtendedAssessmentSection', $section);
        $this->assertEquals('S01', $section->getIdentifier());
        $this->assertEquals('Section 01', $section->getTitle());
        $this->assertTrue($section->isVisible());
        
        $sectionParts = $section->getSectionParts();
        $this->assertEquals(1, count($sectionParts));
        $this->assertInstanceOf('qtism\\data\\AssessmentSectionRef', $sectionParts['SR01']);
        $this->assertEquals('SR01', $sectionParts['SR01']->getIdentifier());
        $this->assertEquals('./SR01.xml', $sectionParts['SR01']->getHref());
        
        $rubricBlockRefs = $section->getRubricBlockRefs();
        $this->assertEquals(1, count($rubricBlockRefs));
        $this->assertInstanceOf('qtism\\data\\content\\RubricBlockRef', $rubricBlockRefs['R01']);
        $this->assertEquals('R01', $rubricBlockRefs['R01']->getIdentifier());
        $this->assertEquals('./R01.xml', $rubricBlockRefs['R01']->getHref());
        
        $this->assertEquals(0, count($section->getRubricBlocks()));
    }
    
    public function testMarshall() {
        $section = new ExtendedAssessmentSection('S01', 'Section 01', true);
        $section->setSectionParts(new SectionPartCollection(array(new AssessmentSectionRef('SR01', './SR01.xml'))));
        $section->setRubricBlockRefs(new RubricBlockRefCollection(array(new RubricBlockRef('R01', './R01.xml'))));
        
        $factory = new CompactMarshallerFactory();
        $marshaller = $factory->createMarshaller($section);
        $elt = $marshaller->marshall($section);
        
        $this->assertEquals('assessmentSection', $elt->nodeName);
        $this->assertEquals('S01', $elt->getAttribute('identifier'));
        $this->assertEquals('Section 01', $elt->getAttribute('title'));
        $this->assertEquals('true', $elt->getAttribute('visible'));
        
        $assessmentSectionRefElts = $elt->getElementsByTagName('assessmentSectionRef');
        $this->assertEquals(1, $assessmentSectionRefElts->length);
        $assessmentSectionRefElt = $assessmentSectionRefElts->item(0);
        $this->assertEquals('SR01', $assessmentSectionRefElt->getAttribute('identifier'));
        $this->assertEquals('./SR01.xml', $assessmentSectionRefElt->getAttribute('href'));
        
        $rubricBlockRefElts = $elt->getElementsByTagName('rubricBlockRef');
        $this->assertEquals(1, $rubricBlockRefElts->length);
        $rubricBlockRefElt = $rubricBlockRefElts->item(0);
        $this->assertEquals('R01', $rubricBlockRefElt->getAttribute('identifier'));
        $this->assertEquals('./R01.xml', $rubricBlockRefElt->getAttribute('href'));
    }
}