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

namespace qtism\data\content\xhtml\tables;

use qtism\common\enums\Enumeration;
use qtism\data\content\BodyElement;

/**
 * The QTI tableCellScope class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TableCellScope implements Enumeration {
    
    /**
     * 
     * @var integer
     */
    const ROW = 0;
    
    /**
     * 
     * @var integer
     */
    const COL = 1;
    
    /**
     * 
     * @var integer
     */
    const ROWGROUP = 2;
    
    /**
     * 
     * @var integer
     */
    const COLGROUP = 3;
   
    public static function asArray() {
        return array(
            'ROW' => self::ROW,
            'COL' => self::COL,
            'ROWGROUP' => self::ROWGROUP,
            'COLGROUP' => self::COLGROUP                
        );
    }
    
    public static function getConstantByName($name) {
        switch (strtolower($name)) {
            case 'row':
                return self::ROW;
            break;
            
            case 'col':
                return self::COL;
            break;
            
            case 'rowgroup':
                return self::ROWGROUP;
            break;
            
            case 'colgroup':
                return self::COLGROUP;
            break;
            
            default:
                return false;
            break;
        }
    }
    
    public static function getNameByConstant($constant) {
        switch ($constant) {
            case self::ROW:
                return 'row';
            break;
            
            case self::COL:
                return 'col';
            break;
            
            case self::ROWGROUP:
                return 'rowgroup';
            break;
            
            case self::COLGROUP:
                return 'colgroup';
            break;
            
            default:
                return false;
            break;
        }
    }
}