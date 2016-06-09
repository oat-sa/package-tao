<?php

use qtism\common\datatypes\Float;
use qtism\runtime\common\MultipleContainer;
use qtism\common\enums\BaseType;
use qtism\common\collections\IdentifierCollection;
use qtism\data\expressions\OutcomeMinimum;
use qtism\runtime\expressions\OutcomeMinimumProcessor;

require_once (dirname(__FILE__) . '/../../../QtiSmItemSubsetTestCase.php');

class OutcomeMinimumProcessorTest extends QtiSmItemSubsetTestCase {
	
    /**
     * @dataProvider outcomeMinimumProvider
     * 
     * @param OutcomeMinimum $expression
     * @param integer $expectedResult
     */
	public function testOutcomeMaximum(OutcomeMinimum $expression, $expectedResult) {
		$session = $this->getTestSession();
		
		$processor = new OutcomeMinimumProcessor($expression);
		$processor->setState($session);
		$result = $processor->process();
		
		if ($expectedResult === null) {
		    $this->assertSame($expectedResult, $result);
		}
		else {
		    $this->assertInstanceOf('qtism\\runtime\\common\\MultipleContainer', $result);
		    $this->assertEquals(BaseType::FLOAT, $result->getBaseType());
		    $this->assertTrue($result->equals($expectedResult));
		    
		}
	}
	
	public function outcomeMinimumProvider() {
	    return array(
	        array(self::getOutcomeMinimum('SCORE'), new MultipleContainer(BaseType::FLOAT, array(new Float(-2.0), new Float(0.5), new Float(1.0), new Float(1.0), new Float(1.0), new Float(1.0)))),
	        array(self::getOutcomeMinimum('SCORE', '', '', new IdentifierCollection(array('minimum'))), new MultipleContainer(BaseType::FLOAT, array(new Float(-2.0), new Float(0.5), new Float(1.0), new Float(1.0), new Float(1.0), new Float(1.0)))),
	        array(self::getOutcomeMinimum('SCORE', 'W01', '', new IdentifierCollection(array('minimum'))), new MultipleContainer(BaseType::FLOAT, array(new Float(-4.0), new Float(1.0), new Float(2.0), new Float(2.0), new Float(2.0), new Float(2.0)))),
	        array(self::getOutcomeMinimum('SCORE', 'W01', '', new IdentifierCollection(array('minimum', 'maximum'))), new MultipleContainer(BaseType::FLOAT, array(new Float(-4.0), new Float(1.0), new Float(2.0), new Float(2.0), new Float(2.0), new Float(2.0)))),
	        array(self::getOutcomeMinimum('SCORE', 'W01'), new MultipleContainer(BaseType::FLOAT, array(new Float(-4.0), new Float(1.0), new Float(2.0), new Float(2.0), new Float(2.0), new Float(2.0)))),
	        array(self::getOutcomeMinimum('SCORE', 'W02', '', new IdentifierCollection(array('minimum'))), new MultipleContainer(BaseType::FLOAT, array(new Float(-2.0), new Float(0.5), new Float(1.0), new Float(1.0), new Float(1.0), new Float(1.0)))), // Weight not found
	    );
	}
	
    protected static function getOutcomeMinimum($outcomeIdentifier, $weightIdentifier = '', $sectionIdentifier = '', IdentifierCollection $includeCategories = null, IdentifierCollection $excludeCategories = null) {
	    $outcomeMinimum = new OutcomeMinimum($outcomeIdentifier);
	    $outcomeMinimum->setSectionIdentifier($sectionIdentifier);
	    
	    if (empty($includeCategories) === false) {
	        $outcomeMinimum->setIncludeCategories($includeCategories);
	    }
	    
	    if (empty($excludeCategories) === false) {
	        $outcomeMinimum->setExcludeCategories($excludeCategories);
	    }
	    
	    if (empty($weightIdentifier) === false) {
	        $outcomeMinimum->setWeightIdentifier($weightIdentifier);
	    }

	    return $outcomeMinimum;
	}
}