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
* Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
*
*/

use qtism\data\AssessmentTest;
use qtism\data\NavigationMode;

/**
* Utility methods for the QtiTest Test compilation process.
*
* @author Jérôme Bogaerts <jerome@taotesting.com>
*
*/
class taoQtiTest_helpers_TestCompilerUtils {
    
    /**
     * Get an  associative array representing some meta-data about the
     * given $test.
     * 
     * The following keys can be accessed:
     * 
     * 'branchRules': whether or not the test definition contains branchRule components in force.
     * 'preConditions': whether or not the test definition contains preCondition components in force.
     * 
     * @param AssessmentTest $test
     * @return array An associative array.
     */
    static public function testMeta(AssessmentTest $test) {
        $meta = array();
        
        $meta['branchRules'] = self::testContainsBranchRules($test);
        $meta['preConditions'] = self::testContainsPreConditions($test);
        
        return $meta;
    }
    
    /**
     * Whether or not a given $test contains branchRules subject to be
     * in force during its execution.
     * 
     * @param AssessmentTest $test
     * @return boolean
     */
    static private function testContainsBranchRules(AssessmentTest $test) {
        $testParts = $test->getComponentsByClassName('testPart');
        $containsBranchRules = false;
        
        foreach ($testParts as $testPart) {
            // Remember that branchRules are ignored when the navigation mode
            // is non linear.
            if ($testPart->getNavigationMode() !== NavigationMode::NONLINEAR) {
                $branchings = $testPart->getComponentsByClassName('branchRule');
                
                if (count($branchings) > 0) {
                    $containsBranchRules = true;
                    break;
                }
            }
        }
        
        return $containsBranchRules;
    }
    
    /**
     * Whether or not a given $test contains preConditions subject to be in force
     * during its execution.
     * 
     * @param AssessmentTest $test
     * @return boolean
     */
    static private function testContainsPreConditions(AssessmentTest $test) {
        $testParts = $test->getComponentsByClassName('testPart');
        $containsPreConditions = false;
        
        foreach ($testParts as $testPart) {
            // PreConditions are only taken into account
            // in linear navigation mode.
            if ($testPart->getNavigationMode() !== NavigationMode::NONLINEAR) {
                $preConditions = $testPart->getComponentsByClassName('preCondition');
                
                if (count($preConditions) > 0) {
                    $containsPreConditions = true;
                    break;
                }
            }
        }
        
        return $containsPreConditions;
    }
}