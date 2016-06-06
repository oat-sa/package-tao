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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 *
 */
namespace oat\taoQtiItem\test;

use common_ext_ExtensionsManager;
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoQtiItem\model\qti\Parser;

/**
 *
 * @author sam, <sam@taotesting.com>
 * @package taoQtiItem

 */
class QtiParsingAltProfileTest extends TaoPhpUnitTestRunner {

	/**
	 * tests initialization
	 */
	public function setUp(){
		TaoPhpUnitTestRunner::initTest();
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
	}
	
    /**
     * test if alternative QTI profiles are managed correctly during parsing
     */
    public function testParseAlternativeProfile(){
        
        $file = dirname(__FILE__).'/samples/xml/qtiv2p1/alternativeProfiles/apip001.xml';
        $qtiParser = new Parser($file);
        $qtiParser->validate();

        if(!$qtiParser->isValid()){
            $this->fail($qtiParser->displayErrors());
        }

        $this->assertTrue($qtiParser->isValid());

        $item = $qtiParser->load();

        $this->assertInstanceOf('\\oat\\taoQtiItem\\model\\qti\\Item', $item);
        
        $xml = simplexml_load_string($item->toXML());
        $this->assertEquals('http://www.imsglobal.org/xsd/apip/apipv1p0/qtiitem/imsqti_v2p1', $xml->getNamespaces()['']);
        $this->assertNotNull($xml->apipAccessibility);
    }

}