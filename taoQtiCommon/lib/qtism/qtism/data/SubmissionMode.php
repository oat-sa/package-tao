<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *   
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package 
 */


namespace qtism\data;

use qtism\common\enums\Enumeration;

/**
 * Submission Mode enumeration.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SubmissionMode implements Enumeration {
	
	const INDIVIDUAL = 0;
	
	const SIMULTANEOUS = 1;
	
	public static function asArray() {
		return array(
			'INDIVIDUAL' => self::INDIVIDUAL,
			'SIMULTANEOUS' => self::SIMULTANEOUS
		);
	}
	
	public static function getConstantByName($name) {
		switch (strtolower($name)) {
			case 'individual':
				return self::INDIVIDUAL;
			break;
			
			case 'simultaneous':
				return self::SIMULTANEOUS;
			break;
			
			default:
				return false;
			break;
		}
	}
	
	public static function getNameByConstant($constant) {
		switch ($constant) {
			case self::INDIVIDUAL:
				return 'individual';
			break;
			
			case self::SIMULTANEOUS:
				return 'simultaneous';
			break;
			
			default:
				return false;
			break;
		}
	}
}
