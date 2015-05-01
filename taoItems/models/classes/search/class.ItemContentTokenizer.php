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
use oat\tao\model\search\tokenizer\RawValue;

/**
 * Item content tokenizer.
 *
 * @author Joel Bout <joel@taotesting.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class taoItems_models_classes_search_ItemContentTokenizer implements Tokenizer
{
    public function getStrings($values)
    {
        $contentStrings = array();
        
        $xmlTokenizer = new taoItems_models_classes_search_XmlItemContentTokenizer();
        $rawTokenizer = new RawValue();
        
        foreach ($values as $valueUri) {
            $file = new core_kernel_file_File($valueUri);
            $content = file_get_contents($file->getAbsolutePath());
            if ($content === false) {
                common_Logger::w('File '.$file->getAbsolutePath().' not found for fileressource '.$itemContent->getUri());
            } else {
                // Try to make it a DOM Document...
                $dom = new DOMDocument('1.0', 'UTF-8');
                
                if (@$dom->loadXML($content) === true) {
                    $contentStrings = array_merge($contentStrings, $xmlTokenizer->getStrings($dom));
                    unset($dom);
                } else {
                    $contentStrings = array_merge($contentStrings, $rawTokenizer->getStrings($content));
                }
            }
        }
        
        return $contentStrings;
    }
}