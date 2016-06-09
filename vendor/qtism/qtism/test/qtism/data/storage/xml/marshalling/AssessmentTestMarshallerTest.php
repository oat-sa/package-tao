<?php

use qtism\data\state\OutcomeDeclaration;
use qtism\data\state\OutcomeDeclarationCollection;
use qtism\common\enums\BaseType;
use qtism\data\expressions\BaseValue;
use qtism\data\rules\SetOutcomeValue;
use qtism\data\rules\OutcomeRuleCollection;
use qtism\data\processing\OutcomeProcessing;
use qtism\data\TestFeedbackCollection;
use qtism\data\TestFeedback;
use qtism\data\TestPartCollection;
use qtism\data\TestPart;
use qtism\data\AssessmentSectionCollection;
use qtism\data\AssessmentSection;
use qtism\data\AssessmentTest;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class AssessmentTestMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		$identifier = 'myAssessmentTest';
		$title = 'My Assessment Test';
		$toolName = 'QTIStateMachine';
		$toolVersion = '1.0b';
		
		$assessmentSections = new AssessmentSectionCollection();
		$assessmentSections[] = new AssessmentSection('myAssessmentSection', 'My Assessment Section', true);
		
		$testParts = new TestPartCollection();
		$testParts[] = new TestPart('myTestPart', $assessmentSections);
		
		$testFeedBacks = new TestFeedbackCollection();
		$testFeedBacks[] = new TestFeedback('myFeedback', 'myOutcome', '<div>Feedback!</div>', 'A Feedback');
		
		$outcomeRules = new OutcomeRuleCollection();
		$outcomeRules[] = new SetOutcomeValue('myOutcome', new BaseValue(BaseType::BOOLEAN, true));
		$outcomeProcessing = new OutcomeProcessing($outcomeRules);
		
		$outcomeDeclarations = new OutcomeDeclarationCollection();
		$outcomeDeclarations[] = new OutcomeDeclaration('myOutcome', BaseType::BOOLEAN);
		
		$component = new AssessmentTest($identifier, $title, $testParts);
		$component->setToolName($toolName);
		$component->setToolVersion($toolVersion);
		$component->setTestFeedbacks($testFeedBacks);
		$component->setOutcomeProcessing($outcomeProcessing);
		$component->setOutcomeDeclarations($outcomeDeclarations);
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('assessmentTest', $element->nodeName);
		$this->assertEquals($identifier, $element->getAttribute('identifier'));
		$this->assertEquals($title, $element->getAttribute('title'));
		$this->assertEquals($toolName, $element->getAttribute('toolName'));
		$this->assertEquals($toolVersion, $element->getAttribute('toolVersion'));
		
		// testParts
		$this->assertEquals(1, $element->getElementsByTagName('testPart')->length);
		$this->assertTrue($element === $element->getElementsByTagName('testPart')->item(0)->parentNode);
		
		// assessmentSections
		$testPart = $element->getElementsByTagName('testPart')->item(0);
		$this->assertEquals(1, $element->getElementsByTagName('assessmentSection')->length);
		$this->assertTrue($testPart === $element->getElementsByTagName('assessmentSection')->item(0)->parentNode);

		// outcomeDeclarations
		$this->assertEquals(1, $element->getElementsByTagName('outcomeDeclaration')->length);
		$this->assertTrue($element === $element->getElementsByTagName('outcomeDeclaration')->item(0)->parentNode);
		
		// testFeedbacks
		$this->assertEquals(1, $element->getElementsByTagName('testFeedback')->length);
		$this->assertTrue($element === $element->getElementsByTagName('testFeedback')->item(0)->parentNode);
		
		// outcomeProcessing
		$this->assertEquals(1, $element->getElementsByTagName('outcomeProcessing')->length);
		$this->assertTrue($element === $element->getElementsByTagName('outcomeProcessing')->item(0)->parentNode);
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML(
			'
			<assessmentTest identifier="myAssessmentTest" title="My Assessment Test" toolName="QTIStateMachine" toolVersion="1.0b">
				<testPart identifier="myTestPart" navigationMode="linear" submissionMode="individual">
					<assessmentSection identifier="myAssessmentSection" title="My Assessment Section" visible="true"/>
				</testPart>
				<testFeedback showHide="true" access="during" outcomeIdentifier="myOutcome" identifier="myFeedback" title="A Feedback">
					<div>Feedback!</div>
				</testFeedback>
				<outcomeDeclaration identifier="myOutcome" baseType="boolean" cardinality="single"/>
				<outcomeProcessing>
					<setOutcomeValue identifier="myOutcome">
						<baseValue baseType="boolean">true</baseValue>
					</setOutcomeValue>
  				</outcomeProcessing>
			</assessmentTest>
			'
		);
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\AssessmentTest', $component);
		$this->assertEquals('myAssessmentTest', $component->getIdentifier());
		$this->assertEquals('My Assessment Test', $component->getTitle());
		$this->assertEquals('QTIStateMachine', $component->getToolName());
		$this->assertEquals('1.0b', $component->getToolVersion());
		$this->assertTrue($component->isExclusivelyLinear());
		
		$this->assertEquals(1, count($component->getTestFeedbacks()));
		$this->assertEquals(1, count($component->getTestParts()));
		$this->assertEquals(1, count($component->getOutcomeDeclarations()));
		$this->assertEquals(1, count($component->getOutcomeProcessing()));
	}
}
