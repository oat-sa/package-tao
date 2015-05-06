<?php
/*
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
 * 
 */

use oat\taoQtiItem\model\qti\Parser;

?>
<?php
require_once dirname(__FILE__).'/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__).'/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoQTI

 */
class QtiOutputTest extends TaoPhpUnitTestRunner
{

    protected $qtiService;

    /**
     * tests initialization
     */
    public function setUp(){
        TaoPhpUnitTestRunner::initTest();
    }

    /**
     * test the building and exporting out the items
     */
    public function testToQTI(){

        $qtiItemFiles = array_merge(
                glob(dirname(__FILE__).'/samples/xml/qtiv2p1/*.xml')
        );

        foreach($qtiItemFiles as $file){

            if(strpos($file, 'media-prompt.xml') === false){
//                continue;
            }

            $qtiParser = new Parser($file);
            $item = $qtiParser->load();

            //test if content has been exported
            $qti = $item->toXML();
            $this->assertFalse(empty($qti));

            //test if it's a valid QTI file
            $tmpFile = $this->createFile('', uniqid('qti_', true).'.xml');
            file_put_contents($tmpFile, $qti);
            $this->assertTrue(file_exists($tmpFile));

            $parserValidator = new Parser($tmpFile);
            $parserValidator->validate();

            if(!$parserValidator->isValid()){
                $this->fail($file.' output invalid :'.$parserValidator->displayErrors().' -> '.$qti);
            }
            /*
              @unlink($tmpFile);
              $this->assertFalse(file_exists($tmpFile));
             */
        }
    }

    /**
     * test the building and exporting out the items
     */
    public function testToXHTML(){

        $doc = new DOMDocument();
        $doc->validateOnParse = true;
        foreach(glob(dirname(__FILE__).'/samples/*.xml') as $file){

            $qtiParser = new Parser($file);
            $item = $qtiParser->load();

            $this->assertTrue($qtiParser->isValid());
            $this->assertNotNull($item);
            $this->assertIsA($item, 'oat\\taoQtiItem\\model\\qti\\Item');

            //test if content has been exported
            $xhtml = $item->toXHTML();
            $this->assertFalse(empty($xhtml));

            try{
                $doc->loadHTML($xhtml);
            }catch(DOMException $de){
                $this->fail($de);
            }
        }
    }

}