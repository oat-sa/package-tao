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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

use oat\tao\test\TaoPhpUnitTestRunner;

include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * This class aims at testing tao_helpers_Xhtml.
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package taoItems
 
 */
class XmlItemContentTokenizerTest extends TaoPhpUnitTestRunner 
{
    public function testSimpleXmlContent()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML('
            <myTag attribute="blabla">
              <p>
                A paragraph
              </p> with some other text...
            </myTag>
        ');
        
        $tokenizer = new taoItems_models_classes_search_XmlItemContentTokenizer();
        $tokens = $tokenizer->getStrings($dom);
        
        $this->assertEquals('A paragraph', $tokens[0]);
        $this->assertEquals('with some other text...', $tokens[1]);
    }
}