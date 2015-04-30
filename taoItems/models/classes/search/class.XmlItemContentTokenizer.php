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

use oat\tao\model\search\tokenizer\Tokenizer;

/**
 * XML Based Item Tokenizer.
 * 
 * This Tokenizer implementation retrieves all text nodes of given
 * XML files as the final list of tokens.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class taoItems_models_classes_search_XmlItemContentTokenizer implements Tokenizer
{
    public function getStrings($values)
    {
        $contentStrings = array();
        
        if (is_array($values) === false) {
            $values = array($values);
        }
        
        foreach ($values as $value) {
            if ($value instanceof DOMDocument) {
                $xpath = new DOMXPath($value);
                $textNodes = $xpath->query('//text()');
                unset($xpath);
                
                foreach ($textNodes as $textNode) {
                    if (ctype_space($textNode->wholeText) === false) {
                        $contentStrings[] = trim($textNode->wholeText);
                    }
                }
            }
        }
        
        return $contentStrings;
    }
}