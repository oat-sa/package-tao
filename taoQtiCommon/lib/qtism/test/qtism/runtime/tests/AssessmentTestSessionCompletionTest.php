<?php

use qtism\common\datatypes\Identifier;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\ResponseVariable;
use qtism\runtime\common\State;

require_once (dirname(__FILE__) . '/../../../QtiSmAssessmentTestSessionTestCase.php');

/**
 * Focus on testing the numberCompleted method of AssessmentTestSession.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentTestSessionCompletionTest extends QtiSmAssessmentTestSessionTestCase {
    
    /**
     * In linear mode, items are considered completed if they were
     * presented at least one time.
     * 
     * Please note that if $identifiers contain 
     * 
     * * 'skip' strings, the item subject to the test will skip the current item instead of ending the attempt.
     * * 'moveNext' strings, the item subject to the test will not be end-attempted and a moveNext will be performed instead.
     * 
     * @dataProvider completionPureLinearProvider
     * @param string $testFile The Compact test definition to be run as a candidate session.
     * @param array $identifiers An array of response identifier to be given for each item.
     * @param integer $finalNumberCompleted The expected number of completed items when the session closes.
     */
    public function testCompletion($testFile, $identifiers, $finalNumberCompleted) {
        
        $session = self::instantiate($testFile);
        $session->beginTestSession();
        
        // Nothing completed at this time.
        $this->assertSame(0, $session->numberCompleted());
        
        $i = 1;
        $movedNext = 0;
        foreach ($identifiers as $identifier) {

            $this->assertSame($i - 1 - $movedNext, $session->numberCompleted());
            
            $session->beginAttempt();
            
            if ($identifier === 'skip') {
                $session->skip();
            }
            else if ($identifier === 'moveNext') {
                $session->moveNext();
                $movedNext++;
            }
            else {
                $session->endAttempt(new State(array(new ResponseVariable('RESPONSE', Cardinality::SINGLE, BaseType::IDENTIFIER, new Identifier($identifier)))));
            }
            
            $this->assertSame($i - $movedNext, $session->numberCompleted());
            
            if ($identifier !== 'moveNext') {
                $session->moveNext();
            }
            
            $i++;
        }
        
        // Final completion check.
        $this->assertSame($finalNumberCompleted, $session->numberCompleted());
        
        // We must reach the end of the test session.
        $this->assertFalse($session->isRunning());
    }
    
    public function completionPureLinearProvider() {
        return array(
            array(self::samplesDir() . '/custom/runtime/linear_5_items.xml', array('skip', 'skip', 'skip', 'skip', 'skip'), 5),
            array(self::samplesDir() . '/custom/runtime/linear_5_items.xml', array('ChoiceA', 'skip', 'ChoiceC', 'ChoiceD', 'ChoiceE'), 5),
            array(self::samplesDir() . '/custom/runtime/linear_5_items.xml', array('ChoiceA', 'ChoiceB', 'ChoiceC', 'ChoiceD', 'ChoiceE'), 5),
            array(self::samplesDir() . '/custom/runtime/completion/linear_10_items_2_testparts.xml', array('skip', 'skip', 'skip', 'skip', 'skip', 'skip', 'skip', 'skip', 'skip', 'skip'), 10),
            array(self::samplesDir() . '/custom/runtime/completion/linear_10_items_2_testparts.xml', array('ChoiceA', 'skip', 'ChoiceC', 'skip', 'ChoiceE', 'skip', 'ChoiceG', 'skip', 'ChoiceI', 'skip'), 10),
            array(self::samplesDir() . '/custom/runtime/completion/linear_10_items_2_testparts.xml', array('ChoiceA', 'ChoiceB', 'ChoiceC', 'ChoiceD', 'ChoiceE', 'ChoiceF', 'ChoiceG', 'ChoiceH', 'ChoiceI', 'ChoiceJ'), 10),
            array(self::samplesDir() . '/custom/runtime/nonlinear_5_items.xml', array('moveNext', 'moveNext', 'moveNext', 'moveNext', 'moveNext'), 0),
            array(self::samplesDir() . '/custom/runtime/nonlinear_5_items.xml', array('ChoiceA', 'moveNext', 'choiceC', 'ChoiceD', 'ChoiceE'), 4),
            array(self::samplesDir() . '/custom/runtime/nonlinear_5_items.xml', array('ChoiceA', 'ChoiceB', 'choiceC', 'ChoiceD', 'ChoiceE'), 5),
            array(self::samplesDir() . '/custom/runtime/completion/nonlinear_10_items_2_testparts.xml', array('moveNext', 'moveNext', 'moveNext', 'moveNext', 'moveNext', 'moveNext', 'moveNext', 'moveNext', 'moveNext', 'moveNext'), 0),
            array(self::samplesDir() . '/custom/runtime/completion/nonlinear_10_items_2_testparts.xml', array('ChoiceA', 'moveNext', 'ChoiceC', 'moveNext', 'ChoiceE', 'moveNext', 'ChoiceG', 'moveNext', 'ChoiceI', 'moveNext'), 5),
            array(self::samplesDir() . '/custom/runtime/completion/nonlinear_10_items_2_testparts.xml', array('ChoiceA', 'ChoiceB', 'ChoiceC', 'ChoiceD', 'ChoiceE', 'ChoiceF', 'ChoiceG', 'ChoiceH', 'ChoiceI', 'ChoiceJ'), 10),
            array(self::samplesDir() . '/custom/runtime/completion/linearnonlinear_10_items_2_testparts.xml', array('ChoiceA', 'ChoiceB', 'ChoiceC', 'ChoiceD', 'ChoiceE', 'ChoiceF', 'ChoiceG', 'ChoiceH', 'ChoiceI', 'ChoiceJ'), 10),
            array(self::samplesDir() . '/custom/runtime/completion/linearnonlinear_10_items_2_testparts.xml', array('skip', 'skip', 'skip', 'skip', 'skip', 'moveNext', 'moveNext', 'moveNext', 'moveNext', 'moveNext'), 5),
            array(self::samplesDir() . '/custom/runtime/completion/linearnonlinear_10_items_2_testparts.xml', array('ChoiceA', 'skip', 'ChoiceC', 'skip', 'ChoiceE', 'moveNext', 'ChoiceG', 'moveNext', 'ChoiceI', 'moveNext'), 7),
        );
    }
}