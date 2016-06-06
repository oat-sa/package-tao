<?php

use qtism\data\AssessmentSectionRef;
use qtism\data\AssessmentItemRef;
use qtism\data\SectionPartCollection;
use qtism\data\ItemSessionControl;
use qtism\data\AssessmentSection;
use qtism\data\rules\PreCondition;
use qtism\data\rules\PreConditionCollection;
use qtism\data\rules\BranchRule;
use qtism\data\rules\BranchRuleCollection;
use qtism\data\expressions\BaseValue;
use qtism\common\enums\BaseType;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class AssessmentSectionMarshallerTest extends QtiSmTestCase {

	public function testMarshallMinimal() {
		
		$identifier = 'myAssessmentSection';
		$title = 'A Minimal Assessment Section';
		$visible = true;
		
		$component = new AssessmentSection($identifier, $title, $visible);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('assessmentSection', $element->nodeName);
		$this->assertEquals($identifier, $element->getAttribute('identifier'));
		$this->assertEquals($title, $element->getAttribute('title'));
		$this->assertEquals('true', $element->getAttribute('visible'));
		$this->assertEquals(0, $element->getElementsByTagName('assessmentSection')->length);
		$this->assertEquals(0, $element->getElementsByTagName('assessmentSectionRef')->length);
		$this->assertEquals(0, $element->getElementsByTagName('assessmentItemRef')->length);
	}
	
	public function testMarshallNotRecursive() {
		$identifier = 'myAssessmentSection';
		$title = 'A non Recursive Assessment Section';
		$visible = true;
		$keepTogether = false;
		
		// preConditions
		$preConditions = new PreConditionCollection();
		$preConditions[] = new PreCondition(new BaseValue(BaseType::BOOLEAN, true));
		
		// branchRules
		$branchRules = new BranchRuleCollection();
		$branchRules[] = new BranchRule(new BaseValue(BaseType::BOOLEAN, false), 'EXIT_TEST');
		
		// itemSessionControl
		$itemSessionControl = new ItemSessionControl();
		$itemSessionControl->setAllowReview(true);
		
		// sectionParts
		$sectionParts = new SectionPartCollection();
		$sectionParts[] = new AssessmentItemRef('Q01', './questions/Q01.xml');
		$sectionParts[] = new AssessmentItemRef('Q02', './questions/Q02.xml');
		$sectionParts[] = new AssessmentSectionRef('S01', './sections/S01.xml');
		
		$component = new AssessmentSection($identifier, $title, $visible);
		$component->setKeepTogether($keepTogether);
		$component->setPreConditions($preConditions);
		$component->setBranchRules($branchRules);
		$component->setItemSessionControl($itemSessionControl);
		$component->setSectionParts($sectionParts);
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\DOMElement', $element);
		$this->assertEquals($identifier, $element->getAttribute('identifier'));
		$this->assertEquals($title, $element->getAttribute('title'));
		$this->assertEquals('true', $element->getAttribute('visible'));
		$this->assertEquals('false', $element->getAttribute('keepTogether'));
		
		$this->assertEquals(1, $element->getElementsByTagName('preCondition')->length);
		$this->assertEquals(1, $element->getElementsByTagName('preCondition')->item(0)->getElementsByTagName('baseValue')->length);
		
		$this->assertEquals(1, $element->getElementsByTagName('branchRule')->length);
		$this->assertEquals(1, $element->getElementsByTagName('branchRule')->item(0)->getElementsByTagName('baseValue')->length);
		
		$this->assertEquals(1, $element->getElementsByTagName('itemSessionControl')->length);
		$this->assertEquals('true', $element->getElementsByTagName('itemSessionControl')->item(0)->getAttribute('allowReview'));
		
		$this->assertEquals(2, $element->getElementsByTagName('assessmentItemRef')->length);
		$this->assertEquals('Q02', $element->getElementsByTagName('assessmentItemRef')->item(1)->getAttribute('identifier'));
		
		$this->assertEquals(1, $element->getElementsByTagName('assessmentSectionRef')->length);
		$this->assertEquals('S01', $element->getElementsByTagName('assessmentSectionRef')->item(0)->getAttribute('identifier'));
	}
	
	public function testMarshallRecursive() {
		// sub1
		$identifier = "sub1AssessmentSection";
		$title = "Sub1 Assessment Section";
		$visible = true;
		$sub1 = new AssessmentSection($identifier, $title, $visible);
		$sub1Parts = new SectionPartCollection();
		$sub1Parts[] = new AssessmentItemRef('Q01', './questions/Q01.xml');
		$sub1Parts[] = new AssessmentItemRef('Q02', './questions/Q02.xml');
		$sub1->setSectionParts($sub1Parts);
		
		// sub21
		$identifier = "sub21AssessmentSection";
		$title = "Sub21 Assessment Section";
		$visible = false;
		$sub21 = new AssessmentSection($identifier, $title, $visible);
		$sub21Parts = new SectionPartCollection();
		$sub21Parts[] = new AssessmentItemRef('Q04', './questions/Q04.xml');
		$sub21->setSectionParts($sub21Parts);
		
		// sub22
		$identifier = "sub22AssessmentSection";
		$title = "Sub22 Assessment Section";
		$visible = true;
		$sub22 = new AssessmentSection($identifier, $title, $visible);
		$sub22Parts = new SectionPartCollection();
		$sub22Parts[] = new AssessmentSectionRef('S01', './sections/S01.xml');
		$sub22->setSectionParts($sub22Parts);
		
		// sub2
		$identifier = "sub2AssessmentSection";
		$title = "Sub2 Assessment Section";
		$visible = true;
		$sub2 = new AssessmentSection($identifier, $title, $visible);
		$sub2Parts = new SectionPartCollection();
		$sub2Parts[] = new AssessmentItemRef('Q03', './questions/Q03.xml');
		$sub2Parts[] = $sub21;
		$sub2Parts[] = $sub22;
		$sub2->setSectionParts($sub2Parts);
		
		// root
		$identifier = "rootAssessmentSection";
		$title = "Root Assessment Section";
		$visible = true;
		$root = new AssessmentSection($identifier, $title, $visible);
		$root->setSectionParts(new SectionPartCollection(array($sub1, $sub2)));
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($root);
		$element = $marshaller->marshall($root);

		$this->assertInstanceOf('qtism\\data\\AssessmentSection', $root);
		$this->assertEquals(4, $element->getElementsByTagName('assessmentSection')->length);
		
		$sub1Elt = $element->getElementsByTagName('assessmentSection')->item(0);
		$this->assertEquals('sub1AssessmentSection', $sub1Elt->getAttribute('identifier'));
		$this->assertTrue($element === $sub1Elt->parentNode);
		$this->assertEquals('Q02', $sub1Elt->getElementsByTagName('assessmentItemRef')->item(1)->getAttribute('identifier'));
		
		$sub2Elt = $element->getElementsByTagName('assessmentSection')->item(1);
		$this->assertEquals('sub2AssessmentSection', $sub2Elt->getAttribute('identifier'));
		$this->assertTrue($element === $sub2Elt->parentNode);
		
		$sub21Elt = $element->getElementsByTagName('assessmentSection')->item(2);
		$this->assertEquals('sub21AssessmentSection', $sub21Elt->getAttribute('identifier'));
		$this->assertTrue($sub2Elt === $sub21Elt->parentNode);
		
		$sub22Elt = $element->getElementsByTagName('assessmentSection')->item(3);
		$this->assertEquals('sub22AssessmentSection', $sub22Elt->getAttribute('identifier'));
		$this->assertTrue($sub2Elt === $sub22Elt->parentNode);
	}
	
	public function testUnmarshallMinimal() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<assessmentSection xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="myAssessmentSection" title="A Minimal Assessment Section" visible="true"/>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\AssessmentSection', $component);
		$this->assertEquals('myAssessmentSection', $component->getIdentifier());
		$this->assertEquals('A Minimal Assessment Section', $component->getTitle());
		$this->assertTrue($component->isVisible());
		$this->assertEquals(0, count($component->getSectionParts()));
	}
	
	public function testUnmarshallNotRecursive() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<assessmentSection identifier="myAssessmentSection" title="A non Recursive Assessment Section" visible="true" keepTogether="false">
	  			<preCondition>
	    			<baseValue baseType="boolean">true</baseValue>
	  			</preCondition>
	  			<branchRule target="EXIT_TEST">
	    			<baseValue baseType="boolean">false</baseValue>
	  			</branchRule>
	  			<itemSessionControl allowReview="true"/>
		        <selection select="1"/>
	  			<assessmentItemRef identifier="Q01" required="false" fixed="false" href="./questions/Q01.xml"/>
	  			<assessmentItemRef identifier="Q02" required="false" fixed="false" href="./questions/Q02.xml"/>
	  			<assessmentSectionRef identifier="S01" required="false" fixed="false" href="./sections/S01.xml"/>
			</assessmentSection>
			'
		);
		$element = $dom->documentElement;
	
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
	
		$this->assertInstanceOf('qtism\\data\\AssessmentSection', $component);
		$this->assertEquals('myAssessmentSection', $component->getIdentifier());
		$this->assertEquals('A non Recursive Assessment Section', $component->getTitle());
		$this->assertTrue($component->isVisible());
		$this->assertFalse($component->mustKeepTogether());
		$this->assertEquals(3, count($component->getSectionParts()));
		
		// Is order preserved?
		$sectionParts = $component->getSectionParts();
		$this->assertInstanceOf('qtism\\data\\AssessmentItemRef', $sectionParts['Q01']);
		$this->assertEquals('Q01', $sectionParts['Q01']->getIdentifier());
		$this->assertInstanceOf('qtism\\data\\AssessmentItemRef', $sectionParts['Q02']);
		$this->assertEquals('Q02', $sectionParts['Q02']->getIdentifier());
		$this->assertInstanceOf('qtism\\data\\AssessmentSectionRef', $sectionParts['S01']);
		$this->assertEquals('S01', $sectionParts['S01']->getIdentifier());
		
		$this->assertEquals(1, count($component->getPreconditions()));
		$this->assertEquals(1, count($component->getBranchRules()));
		$this->assertTrue($component->getItemSessionControl()->doesAllowReview());
		
		// Does it contain a selection?
		$this->assertTrue($component->hasSelection());
		
		// Does it contain an itemSessionControl?
		$this->assertTrue($component->hasItemSessionControl());
		
		// Does it contain a preCondition?
		$this->assertEquals(1, count($component->getPreconditions()));
		
		// Does it contain a branchRule?
		$this->assertEquals(1, count($component->getBranchRules()));
	}
	
	public function testUnmarshallRecursive() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<assessmentSection xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="rootAssessmentSection" title="Root Assessment Section" visible="true">
				<selection select="2" withReplacement="true"/>
		        <assessmentSection identifier="sub1AssessmentSection" title="Sub1 Assessment Section" visible="true">
					<assessmentItemRef identifier="Q01" href="./questions/Q01.xml"/>
					<assessmentItemRef identifier="Q02" href="./questions/Q02.xml"/>
				</assessmentSection>
				<assessmentSection identifier="sub2AssessmentSection" title="Sub2 Assessment Section" visible="true">
		            <selection select="1"/>
					<assessmentItemRef identifier="Q03" href="./questions/Q03.xml"/>
					<assessmentSection identifier="sub21AssessmentSection" title="Sub21 Assessment Section" visible="false">
						<assessmentItemRef identifier="Q04" href="./questions/Q04.xml"/>
					</assessmentSection>
					<assessmentSection identifier="sub22AssessmentSection" title="Sub22 Assessment Section" visible="true">
						<assessmentSectionRef identifier="S01" href="./sections/S01.xml"/>
					</assessmentSection>
				</assessmentSection>
			</assessmentSection>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\AssessmentSection', $component);
		$this->assertEquals('rootAssessmentSection', $component->getIdentifier());
		$this->assertEquals(2, count($component->getSectionParts()));
		$this->assertTrue($component->hasSelection());
		$this->assertEquals(2, $component->getSelection()->getSelect());
		
		$sectionParts = $component->getSectionParts();
		$this->assertEquals('sub1AssessmentSection', $sectionParts['sub1AssessmentSection']->getIdentifier());
		
		$subSectionParts = $sectionParts['sub1AssessmentSection']->getSectionParts();
		$this->assertEquals('Q01', $subSectionParts['Q01']->getIdentifier());
		$this->assertEquals('Q02', $subSectionParts['Q02']->getIdentifier());
		$this->assertEquals('sub2AssessmentSection', $sectionParts['sub2AssessmentSection']->getIdentifier());
		$this->assertTrue($sectionParts['sub2AssessmentSection']->hasSelection());
		
		$subSectionParts = $sectionParts['sub2AssessmentSection']->getSectionParts();
		$this->assertEquals('Q03', $subSectionParts['Q03']->getIdentifier());
		$this->assertEquals('sub21AssessmentSection', $subSectionParts['sub21AssessmentSection']->getIdentifier());
		$this->assertEquals('sub22AssessmentSection', $subSectionParts['sub22AssessmentSection']->getIdentifier());
		
		$subSectionParts = $subSectionParts['sub22AssessmentSection']->getSectionParts();
		$this->assertEquals('S01', $subSectionParts['S01']->getIdentifier());
	}
	
	public function testUnmarshallOneSectionAssessmentItemRefOnly() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<assessmentSection identifier="section" fixed="false" title="My Section" visible="false">
				<assessmentItemRef identifier="Q01" href="Q01.xml" fixed="false"/>
				<assessmentItemRef identifier="Q02" href="Q02.xml" fixed="false"/>
				<assessmentItemRef identifier="Q03" href="Q03.xml" fixed="false"/>
			</assessmentSection>
			'
		);
		
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\AssessmentSection', $component);
		$assessmentItemRefs = $component->getSectionParts();
		$this->assertEquals(3, count($assessmentItemRefs));
	}
}
