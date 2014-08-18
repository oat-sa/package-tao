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

namespace qtism\data\content\interactions;

use qtism\common\enums\Enumeration;

class TextFormat implements Enumeration {
    
    /**
     * From IMS QTI:
     * 
     * Indicates that the text to be entered by the candidate is plain text. 
     * This format is suitable for short unstructured responses. Delivery 
     * engines should preserve white-space characters in candidate input 
     * except where a response consists only of white-space characters, 
     * in which case it should be treated as an empty string (NULL).
     * 
     * @var integer
     */
    const PLAIN = 0;
    
    /**
     * From IMS QTI:
     * 
     * Indicates that the text to be entered by the candidate is pre-formatted
     * and should be rendered in a way consistent with the definition of pre 
     * in [XHTML]. Delivery engines must preserve white-space characters except 
     * where a response consists only of white-space characters, in which case 
     * it should be treated as an empty string (NULL).
     * 
     * @var integer
     */
    const PRE_FORMATTED = 1;
    
    /**
     * From IMS QTI:
     * 
     * Indicates that the text to be entered by the candidate is structured text.
     * The value of the response variable is text marked up in XHTML. The delivery 
     * engine should present an interface suitable for capturing structured text, 
     * this might be plain typed text interpreted with a set of simple text markup 
     * conventions such as those used in wiki page editors or a complete WYSIWYG 
     * editor.
     * 
     * @var integer
     */
    const XHTML = 2;
    
    public static function asArray() {
        return array(
            'PLAIN' => self::PLAIN,
            'PRE_FORMATTED' => self::PRE_FORMATTED,
            'XHTML' => self::XHTML
        );
    }
    
    public static function getConstantByName($name) {
        switch (strtolower($name)) {
            
            case 'plain':
                return self::PLAIN;
            break;
            
            case 'preformatted':
                return self::PRE_FORMATTED;
            break;
            
            case 'xhtml':
                return self::XHTML;
            break;
            
            default:
                return false;
            break;
            
        }
    }
    
    public static function getNameByConstant($constant) {
        switch ($constant) {
            
            case self::PLAIN:
                return 'plain';
            break;
            
            case self::PRE_FORMATTED:
                return 'preFormatted';
            break;
            
            case self::XHTML:
                return 'xhtml';
            break;
            
            default:
                return false;
            break;
        }
    }
}