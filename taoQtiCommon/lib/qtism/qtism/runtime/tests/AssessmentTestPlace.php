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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */

namespace qtism\runtime\tests;

use qtism\common\enums\Enumeration;

/**
 * This enumerations regroups all the "places" a candidate can meet
 * during an AssessmentTest session.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentTestPlace implements Enumeration {
    
    /**
     * Represents the concept of TestPart in an AssessmentTest.
     * 
     * @var integer
     */
    const TEST_PART = 1;
    
    /**
     * Represents the concept of AssessmentSection in an AssessmentTest.
     * 
     * @var integer
     */
    const ASSESSMENT_SECTION = 2;
    
    /**
     * Represents the concept of AssessmentItem in an AssessmentTest.
     * 
     * @var integer
     */
    const ASSESSMENT_ITEM = 4;
    
    /**
     * Represents the concept of AssessmentTest (in an AssessmentTest).
     * 
     * @var integer
     */
    const ASSESSMENT_TEST = 8;
    
    static public function asArray() {
        return array('TEST_PART' => self::TEST_PART,
                       'ASSESSMENT_SECTION' => self::ASSESSMENT_SECTION,
                       'ASSESSMENT_ITEM' => self::ASSESSMENT_ITEM,
                       'ASSESSMENT_TEST' => self::ASSESSMENT_TEST);
    }
    
    static public function getConstantByName($name) {
        switch (strtolower($name)) {
            case 'testpart':
                return self::TEST_PART;    
            break;
            
            case 'assessmentsection':
                return self::ASSESSMENT_SECTION;
            break;
            
            case 'assessmentitem':
                return self::ASSESSMENT_ITEM;
            break;
            
            case 'assessmenttest':
                return self::ASSESSMENT_TEST;
            break;
            
            default:
                return false;
            break;
        }
    }
    
    static public function getNameByConstant($constant) {
        switch ($constant) {
            case self::TEST_PART:
                return 'testPart';
            break;
            
            case self::ASSESSMENT_SECTION:
                return 'assessmentSection';
            break;
            
            case self::ASSESSMENT_ITEM:
                return 'assessmentItem';
            break;
            
            case self::ASSESSMENT_TEST:
                return 'assessmentTest';
            break;
            
            default:
                return false;
            break;
        }
    }
}