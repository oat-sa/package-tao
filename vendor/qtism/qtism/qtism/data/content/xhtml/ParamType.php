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

namespace qtism\data\content\xhtml;

use qtism\common\enums\Enumeration;

class ParamType implements Enumeration {
    
    /**
     * DATA
     * 
     * @var integer
     */
    const DATA = 0;
    
    /**
     * REF
     * 
     * @var integer
     */
    const REF = 1;
    
    public static function asArray() {
        return array(
            'DATA' => self::DATA,
            'REF' => self::REF        
        );
    }
    
    public static function getConstantByName($name) {
        switch (strtolower($name)) {
            case 'data':
                return self::DATA;
            break;
            
            case 'ref':
                return self::REF;
            break;
            
            default:
                return false;
            break;
        }
    }
    
    public static function getNameByConstant($constant) {
        switch ($constant) {
            case self::DATA:
                return 'DATA';
            break;
            
            case self::REF:
                return 'REF';
            break;
            
            default:
                return false;
            break;
        }
    }
}