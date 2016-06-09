<?php

use qtism\common\datatypes\Float;
use qtism\runtime\common\MultipleContainer;
use qtism\common\enums\BaseType;
use qtism\common\collections\IdentifierCollection;
use qtism\data\expressions\OutcomeMaximum;
use qtism\runtime\expressions\OutcomeMaximumProcessor;

require_once (dirname(__FILE__) . '/../../../QtiSmItemSubsetTestCase.php');

class OutcomeMaximumProcessorTest extends QtiSmItemSubsetTestCase {
	
    /**
     * @dataProvider outcomeMaximumProvider
     * 
     * @param OutcomeMaximum $expression
     * @param integer $expectedResult
     */
	public function testOutcomeMaximum(OutcomeMaximum $expression, $expectedResult) {
		$session = $this->getTestSession();
		
		$processor = new OutcomeMaximumProcessor($expression);
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
	
	public function outcomeMaximumProvider() {
	    return array(
	        array(self::getOutcomeMaximum('SCORE'), null), // NULL values involved, the expression returns NULL systematically.
	        array(self::getOutcomeMaximum('SCOREX'), null), // No variable at all matches.
	        array(self::getOutcomeMaximum('SCORE', '', '', new IdentifierCollection(array('maximum'))), new MultipleContainer(BaseType::FLOAT, array(new Float(2.5), new Float(1.5)))),
	        array(self::getOutcomeMaximum('SCORE', 'W0X', '', new IdentifierCollection(array('maximum'))), new MultipleContainer(BaseType::FLOAT, array(new Float(2.5), new Float(1.5)))), // Weight not found then not applied.
	        array(self::getOutcomeMaximum('SCORE', 'W01', '', new IdentifierCollection(array('maximum'))), new MultipleContainer(BaseType::FLOAT, array(new Float(5.0), new Float(3.0)))),
	        array(self::getOutcomeMaximum('SCORE', '', 'S01', new IdentifierCollection(array('maximum'))), new MultipleContainer(BaseType::FLOAT, array(new Float(2.5)))),
	        array(self::getOutcomeMaximum('SCORE', '', 'S02', new IdentifierCollection(array('maximum'))), new MultipleContainer(BaseType::FLOAT, array(new Float(1.5)))),
	        array(self::getOutcomeMaximum('SCORE', 'W01', 'S01', new IdentifierCollection(array('maximum'))), new MultipleContainer(BaseType::FLOAT, array(new Float(5.0)))),
	        array(self::getOutcomeMaximum('SCORE', 'W01', 'S02', new IdentifierCollection(array('maximum'))), new MultipleContainer(BaseType::FLOAT, array(new Float(3.0))))
	    );
	}
	
    protected static function getOutcomeMaximum($outcomeIdentifier, $weightIdentifier = '', $sectionIdentifier = '', IdentifierCollection $includeCategories = null, IdentifierCollection $excludeCategories = null) {
	    $outcomeMaximum = new OutcomeMaximum($outcomeIdentifier);
	    $outcomeMaximum->setSectionIdentifier($sectionIdentifier);
	    
	    if (empty($includeCategories) === false) {
	        $outcomeMaximum->setIncludeCategories($includeCategories);
	    }
	    
	    if (empty($excludeCategories) === false) {
	        $outcomeMaximum->setExcludeCategories($excludeCategories);
	    }
	    
	    if (empty($weightIdentifier) === false) {
	        $outcomeMaximum->setWeightIdentifier($weightIdentifier);
	    }

	    return $outcomeMaximum;
	}
}