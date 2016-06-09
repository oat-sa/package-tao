<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

use qtism\common\datatypes\Pair;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\runtime\common\Variable;
use qtism\common\datatypes\Point;

/**
 * This helper class gives a way to build a State (a collection of variables)
 * to be transmitted to the client-side.
 * 
 * The result output can be accessed with the tao_helpers_StateOutput::getOutput() method
 * which returns an associative array, that can be transformed as needed for transmission
 * to the client-side.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class taoQtiCommon_helpers_LegacyStateOutput extends taoQtiCommon_helpers_AbstractStateOutput {
    
    
    /**
     * Create a new tao_helpers_StateOutput object.
     * 
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get the output array. The returned array is an associative array where keys are the identifier
     * of the variable and the values are the actual values of the variable.
     * 
     * Each variable value is represented as an array, even if the cardinality of the variable is SINGLE. The values
     * are always plain string, as the values coming in from the client-side. Find below an example of returned state
     * output. Please be carefull will null values. They will be transformed as empty strings.
     * 
     * <code>
     * // The  $stateOutput variable is a StateOutput object where the following variables
     * // where added through the StateOutput::addVariable() method.
     * // - OutcomeVariable => 'SCORE1', Cardinality:SINGLE, BaseType:INTEGER, value: 25
     * // - OutcomeVariable => 'SCORE2', Cardinality:SINGLE, BaseType:URI, value: 'http://bit.ly'
     * // - OutcomeVariable => 'SCORE3', Cardinality:SINGLE, BaseType:BOOLEAN, value: false
     * // - OutcomeVariable => 'SCORE4', Cardinality:SINGLE, BaseType:FLOAT, value: null
     * // - OutcomeVariable => 'SCORE5', Cardinality::ORDERED, BaseType:INTEGER, value: {0, -10, null, 44}
     * // - OutcomeVariable => 'SCORE6', Cardinality::MULTIPLE, BaseType:PAIR, value: [new Pair('A', 'B'), new Pair('C', 'D')]
     * // - OutcomeVariable => 'SCORE7', Cardinality::MULTIPLE, BaseType:POINT, value: [new Point(0, 0), new Point(-3, 4)]
     * // - OutcomeVariable => 'SCORE8', Cardinality::RECORD, value: <'val1': 25.3, 'val2': null, 'val3': new Duration('PT1S')>
     * $stateOutput = new taoQtiCommon_helpers_LegacyStateOutput();
     * // ...
     * // $stateOutput->addVariable() ...
     * // $stateOutput->addVariable() ...
     * // $stateOutput->addVariable() ...
     * // ..
     * 
     * $output = $stateOutput->getOutput();
     * 
     * // The content of $output is:
     * // array(
     * //     'SCORE1' => array('25'),
     * //     'SCORE2' => array('http://bit.ly'),
     * //     'SCORE3' => array('false'),
     * //     'SCORE4' => array(''),
     * //     'SCORE5' => array('0', '-10', '', '44'),
     * //     'SCORE6' => array(array('A', 'B'), array('C', 'D')),
     * //     'SCORE7' => array(array('0', '0'), array('-3', '4'))
     * //     'SCORE8' => array('val1' => '25.3', 'val2' => '', 'val3' => 'PT1S')
     * // );
     * </code>
     * 
     * @return array An array of QTI Variables and their values formatted as explained supra.
     */
    public function &getOutput() {
        return parent::getOutput();
    }
    
    /**
     * Add a variable to the State that has to be built for the client-side. The final output
     * array is available through the tao_helpers_StateOutput::getOutput() method.
     * 
     * @param Variable $variable A QTI Runtime Variable
     */
    public function addVariable(Variable $variable) {
        $output = &$this->getOutput();
        $baseType = $variable->getBaseType();
        $cardinality = $variable->getCardinality();
        $varName = $variable->getIdentifier();
        
        if ($cardinality === Cardinality::SINGLE) {
            $singleValue = $variable->getValue();
            if (is_null($singleValue) === true) {
                $output[$varName] = array('');
                return;
            }
            
            $values = array($variable->getValue());
        }
        else {
            $containerValue = $variable->getValue();
            if (is_null($containerValue) === true || $containerValue->isNull() === true) {
                $output[$varName] = array();
                return;
            }
            
            $values = $containerValue->getArrayCopy(true);
        }
        
        $output[$varName] = array();
        
        foreach ($values as $val) {
            if (is_null($val) === true) {
                $output[$varName][] = '';   
            }
            else if (gettype($val) === 'boolean') {
                $output[$varName][] = ($val === true) ? 'true' : 'false';
            }
            else if ($val instanceof Point) {
                $output[$varName][] = array('' . $val->getX(), '' . $val->getY());
            }
            else if ($val instanceof Pair) {
                $output[$varName][] = array($val->getFirst(), $val->getSecond());
            }
            else if (gettype($val) === 'object') {
                $output[$varName][] = $val->__toString();
            }
            else {
                $output[$varName][] = '' . $val;
            }
        }
    }
}