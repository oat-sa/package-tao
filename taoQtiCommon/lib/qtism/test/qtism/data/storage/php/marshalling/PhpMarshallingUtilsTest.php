<?php

use qtism\common\datatypes\Pair;
use qtism\common\datatypes\Point;
use qtism\data\storage\php\marshalling\Utils as PhpMarshallingUtils;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class PhpMarshallingUtilsTest extends QtiSmTestCase {
	
    /**
     * @dataProvider variableNameDataProvider
     * @param mixed $value
     * @param integer $occurence
     * @param string $expected
     */
    public function testVariableName($value, $occurence, $expected) {
        $this->assertEquals($expected, PhpMarshallingUtils::variableName($value, $occurence));
    }
    
    public function variableNameDataProvider() {
        return array(
            array(null, 0, 'nullvalue_0'),
            array(null, 1, 'nullvalue_1'),
            array('string!', 0, 'string_0'),
            array('string!', 2, 'string_2'),
            array(-23, 0, 'integer_0'),
            array(200, 3, 'integer_3'),
            array(34.3, 0, 'double_0'),
            array(24.3, 4, 'double_4'),
            array(true, 0, 'boolean_0'),
            array(false, 5, 'boolean_5'),
            array(new Point(0, 0), 0, 'point_0'),
            array(new Pair('A', 'B'), 6, 'pair_6'),
            array(array('a', true, false), 0, 'array_0'),
            array(array(), 7, 'array_7')
        );
    }
}