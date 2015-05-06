<?php

use qtism\common\collections\IdentifierCollection;

use qtism\data\expressions\NumberPresented;
use qtism\runtime\expressions\NumberPresentedProcessor;

require_once (dirname(__FILE__) . '/../../../QtiSmItemSubsetTestCase.php');

class NumberPresentedProcessorTest extends QtiSmItemSubsetTestCase {
	
    /**
     * @dataProvider numberPresentedProvider
     * 
     * @param NumberPresented $expression
     * @param array $expectedResults
     */
	public function testNumberPresented(NumberPresented $expression, array $expectedResults) {
		$session = $this->getTestSession();
		$processor = new NumberPresentedProcessor($expression);
		$processor->setState($session);
		
		// At the moment, nothing presented.
		$result = $processor->process();
		$this->assertEquals(0, $result->getValue());
		
		for ($i = 0; $i < $session->getRouteCount(); $i++) {
		    $session->beginAttempt();
		    $processor = new NumberPresentedProcessor($expression);
		    $processor->setState($session);
		    $result = $processor->process();
		    
		    $this->assertEquals($expectedResults[$i], $result->getValue());
		    $session->skip();
		    $session->moveNext();
		}
	}
	
	public function numberPresentedProvider() {
	    return array(
	        array(self::getNumberPresented(), array(1, 2, 3, 4, 5, 6, 7, 8, 9)),
	        array(self::getNumberPresented('S01'), array(1, 2, 3, 3, 3, 3, 3, 3, 3)),
	        array(self::getNumberPresented('S02'), array(0, 0, 0, 1, 2, 3, 3, 3, 3)),
	        array(self::getNumberPresented('', new IdentifierCollection(array('mathematics'))), array(1, 1, 2, 2, 2, 3, 3, 3, 3)),
	        array(self::getNumberPresented('S01', new IdentifierCollection(array('mathematics'))), array(1, 1, 2, 2, 2, 2, 2, 2, 2)),
	        array(self::getNumberPresented('', null, new IdentifierCollection(array('mathematics'))), array(0, 1, 1, 2, 3, 3, 4, 5, 6)),
	        array(self::getNumberPresented('S02', null, new IdentifierCollection(array('mathematics'))), array(0, 0, 0, 1, 2, 2, 2, 2, 2)),
	        array(self::getNumberPresented('S03'), array(0, 0, 0, 0, 0, 0, 1, 2, 3))
	    );
	}
	
	protected static function getNumberPresented($sectionIdentifier = '', IdentifierCollection $includeCategories = null, IdentifierCollection $excludeCategories = null) {
	    $numberPresented = new NumberPresented();
	    $numberPresented->setSectionIdentifier($sectionIdentifier);
	    
	    if (empty($includeCategories) === false) {
	        $numberPresented->setIncludeCategories($includeCategories);
	    }
	    
	    if (empty($excludeCategories) === false) {
	        $numberPresented->setExcludeCategories($excludeCategories);
	    }

	    return $numberPresented;
	}
}