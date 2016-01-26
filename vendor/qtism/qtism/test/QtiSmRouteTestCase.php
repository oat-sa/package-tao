<?php

require_once(dirname(__FILE__) . '/../qtism/qtism.php');
require_once(dirname(__FILE__) . '/QtiSmTestCase.php');

use qtism\data\AssessmentTest;
use qtism\data\AssessmentItemRefCollection;
use qtism\data\AssessmentItemRef;
use qtism\data\AssessmentSectionCollection;
use qtism\data\AssessmentSection;
use qtism\data\TestPartCollection;
use qtism\data\TestPart;
use qtism\runtime\tests\Route;

abstract class QtiSmRouteTestCase extends QtiSmTestCase {
    
	public function setUp() {
	    parent::setUp();
	}
	
	public function tearDown() {
	    parent::tearDown();
	}
	
	/**
	 * Build a simple route:
	 *
	 * * Q1 - S1 - T1
	 * * Q2 - S1 - T1
	 * * Q3 - S1 - T1
	 *
	 * @return Route
	 */
	public static function buildSimpleRoute($routeClass = 'qtism\\runtime\\tests\\Route') {
	    $assessmentItemRefs = new AssessmentItemRefCollection();
	    $assessmentItemRefs[] = new AssessmentItemRef('Q1', 'Q1.xml');
	    $assessmentItemRefs[] = new AssessmentItemRef('Q2', 'Q2.xml');
	    $assessmentItemRefs[] = new AssessmentItemRef('Q3', 'Q3.xml');
	
	    $assessmentSections = new AssessmentSectionCollection();
	    $assessmentSections[] = new AssessmentSection('S1', 'Section 1', true);
	    $assessmentSections['S1']->setSectionParts($assessmentItemRefs);
	
	    $testParts = new TestPartCollection();
	    $testParts[] = new TestPart('T1', $assessmentSections);
	    $assessmentTest = new AssessmentTest('test', 'A Test', $testParts);
	
	    $route = new $routeClass();
	
	    $route->addRouteItem($assessmentItemRefs['Q1'], $assessmentSections['S1'], $testParts['T1'], $assessmentTest);
	    $route->addRouteItem($assessmentItemRefs['Q2'], $assessmentSections['S1'], $testParts['T1'], $assessmentTest);
	    $route->addRouteItem($assessmentItemRefs['Q3'], $assessmentSections['S1'], $testParts['T1'], $assessmentTest);
	
	    return $route;
	}
}