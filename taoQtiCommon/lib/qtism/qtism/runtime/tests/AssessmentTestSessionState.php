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
 * The AssessmentTestSessionState enumeration describe the possible state
 * a test session can get during its lifecycle.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentTestSessionState implements Enumeration {
    
    const INITIAL = 0;
    
    const INTERACTING = 1;
    
    const MODAL_FEEDBACK = 2;
    
    const SUSPENDED = 3;
    
    const CLOSED = 4;
    
    public static function asArray() {
        return array(
            'INITIAL' => self::INITIAL,
            'INTERACTING' => self::INTERACTING,
            'MODAL_FEEDBACK' => self::MODAL_FEEDBACK,
            'SUSPENDED' => self::SUSPENDED,
            'CLOSED' => self::CLOSED
        );
    }
    
    public static function getConstantByName($name) {
		switch (strtolower($name)) {
		    
			case 'initial':
				return self::INITIAL;
			break;
			
			case 'interacting':
				return self::INTERACTING;
			break;
			
			case 'modalfeedback':
				return self::MODAL_FEEDBACK;
			break;
			
			case 'suspended':
				return self::SUSPENDED;
			break;
			
			case 'closed':
				return self::CLOSED;
			break;
			
			default:
				return false;
			break;
		}
	}
	
	public static function getNameByConstant($constant) {
	    switch ($constant) {
	        
	        case self::INITIAL:
	            return 'initial';
	        break;
	            	
	        case self::INTERACTING:
	            return 'interacting';
	        break;
	            	
	        case self::MODAL_FEEDBACK:
	            return 'modalFeedback';
	        break;
	            	
	        case self::SUSPENDED:
	            return 'suspended';
	        break;
	            	
	        case self::CLOSED:
	            return 'closed';
	        break;
	            	
	        default:
	            return false;
	        break;
	    }
	}
}