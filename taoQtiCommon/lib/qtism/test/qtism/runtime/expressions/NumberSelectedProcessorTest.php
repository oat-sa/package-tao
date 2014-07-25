<?php


use qtism\common\collections\IdentifierCollection;
use qtism\data\expressions\NumberSelected;
use qtism\runtime\expressions\NumberSelectedProcessor;

require_once (dirname(__FILE__) . '/../../../QtiSmItemSubsetTestCase.php');

class NumberSelectedProcessorTest extends QtiSmItemSubsetTestCase {
	
    /**
     * @dataProvider numberSelectedProvider
     * 
     * @param NumberSelected $expression
     * @param integer $expectedResult
     */
	public function testNumberSelected(NumberSelected $expression, $expectedResult) {
		$session = $this->getTestSession();
		
		// The test is totally linear, the selection is then complete
		// when AssessmentTestSession::beginTestSession is called.
		$processor = new NumberSelectedProcessor($expression);
		$processor->setState($session);
		$result = $processor->process();
		$this->assertEquals($expectedResult, $result->getValue());
	}
	
	public function numberSelectedProvider() {
	    return array(
	        array(self::getNumberSelected(), 9),
	        array(self::getNumberSelected('', new IdentifierCollection(array('mathematics', 'chemistry'))), 4),
	        array(self::getNumberSelected('S01', new IdentifierCollection(array('mathematics', 'chemistry'))), 2),
	        array(self::getNumberSelected('', null, new IdentifierCollection(array('mathematics'))), 6)
	    );
	}
	
    protected static function getNumberSelected($sectionIdentifier = '', IdentifierCollection $includeCategories = null, IdentifierCollection $excludeCategories = null) {
	    $numberSelected = new NumberSelected();
	    $numberSelected->setSectionIdentifier($sectionIdentifier);
	    
	    if (empty($includeCategories) === false) {
	        $numberSelected->setIncludeCategories($includeCategories);
	    }
	    
	    if (empty($excludeCategories) === false) {
	        $numberSelected->setExcludeCategories($excludeCategories);
	    }

	    return $numberSelected;
	}
}