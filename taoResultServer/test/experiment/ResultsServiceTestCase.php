<?php

//TODO simpletest testcase that need to be migrate to phpunit

include_once dirname(__FILE__) . '/../includes/raw_start.php';

class ResultsServiceTestCase extends UnitTestCase {
	/**
	 * tests initialization
	 */
	 /**
	  *
	  * @var taoResultServer_models_classes_LocalResultsService
	  */
	private $localResultsService;
	public function setUp(){		
		TaoTestRunner::initTest();
		$this->localResultsService = new taoResultServer_models_classes_LocalResultsService("ontology");
	}
	public function testLocalResultsService(){
		$this->assertIsA($this->localResultsService, 'taoResultServer_models_classes_LocalResultsService');
	}
	public function testGetAssessmentResult(){
		$this->assertIsA($this->localResultsService->getAssessmentResult(), 'taoResultServer_models_classes_assessmentResult' );
	}
	public function testResultsServerModel(){
	    $context = new taoResultServer_models_classes_Context();
	    $sessionIdentifier = new taoResultServer_models_classes_SessionIdentifier();
	    $sessionIdentifier->setSourceID("#TAODeliveryx_001");
	    $sessionIdentifier->setIdentifier("MyUniqueSession");
	    $context->addSessionIdentifier($sessionIdentifier);
	    $context->setSourcedID("MyUniqueTestTaker");
	    $this->localResultsService->getAssessmentResult()->setContext($context);
	    $this->assertIsA( $this->localResultsService->getAssessmentResult()->getContext(),'taoResultServer_models_classes_context');
	    $sessionIdentifier = current($this->localResultsService->getAssessmentResult()->getContext()->getSessionIdentifiers());

	    $this->assertIsA( $sessionIdentifier,'taoResultServer_models_classes_sessionIdentifier');
	    $this->assertEqual( $sessionIdentifier->getIdentifier(),'MyUniqueSession');
	    $this->assertEqual( $sessionIdentifier->getSourceID(),'#TAODeliveryx_001');

	    

	}
}
?>