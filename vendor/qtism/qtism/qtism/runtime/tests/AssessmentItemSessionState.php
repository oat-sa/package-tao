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
 * The AssessmentItemSessionState enumeration describes the possible
 * states an item session can get during its lifecycle.
 * 
 * @author Jérôme Bogaerts
 *
 */
class AssessmentItemSessionState extends AssessmentTestSessionState {
	
    const NOT_SELECTED = 255;
    
	const SOLUTION = 5;
	
	const REVIEW = 6;
	
	public static function asArray() {
		return array_merge(AssessmentTestSessionState::asArray(), array(
		    'NOT_SELECTED' => self::NOT_SELECTED,
			'SOLUTION' => self::SOLUTION,
			'REVIEW' => self::REVIEW
		));
	}
	
	public static function getConstantByName($name) {
		switch (strtolower($name)) {
		    case 'notselected':
		        return self::NOT_SELECTED;
		    break;
		    
			case 'solution':
				return self::SOLUTION;
			break;
			
			case 'review':
				return self::REVIEW;
			break;
			
			default:
				return AssessmentTestSessionState::getConstantByName($name);
			break;
		}
	}
	
	public static function getNameByConstant($constant) {
		switch ($constant) {
		    case self::NOT_SELECTED:
		        return 'notSelected';
		    break;
		    
			case self::SOLUTION:
				return 'solution';
			break;
			
			case self::REVIEW:
				return 'review';
			break;
			
			default:
				return AssessmentTestSessionState::getNameByConstant($constant);
			break;
		}
	}
}