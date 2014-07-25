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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
?>
<?php
require_once dirname(__FILE__).'/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__).'/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoQTI
 * @subpackage test
 */
class QTIOutputTestCase extends TestCasePrototype
{

    protected $qtiService;

    /**
     * tests initialization
     */
    public function setUp(){
        TaoTestRunner::initTest();

        $parameters = array(
            'root_url' => ROOT_URL,
            'base_www' => BASE_WWW,
            'taobase_www' => TAOBASE_WWW,
            'qti_lib_www' => BASE_WWW.'js/QTI/',
            'qti_base_www' => BASE_WWW.'js/QTI/',
            'raw_preview' => false,
            'debug' => false
        );
        taoItems_models_classes_TemplateRenderer::setContext($parameters, 'ctx_');
    }

    /**
     * test the building and exporting out the items
     */
    public function testToQTI(){

        $qtiItemFiles = array_merge(
                glob(dirname(__FILE__).'/samples/xml/qtiv2p1/*.xml')
//                glob(dirname(__FILE__).'/samples/cito/TOA_NoExtensions/items/*.xml'),
//                glob(dirname(__FILE__).'/samples/cito/TAO_Extensions/depitems/*.xml')
        );

        foreach($qtiItemFiles as $file){

            if(strpos($file, 'media-prompt.xml') === false){
//                continue;
            }

            $qtiParser = new taoQTI_models_classes_QTI_Parser($file);
            $item = $qtiParser->load();
            
            //test if content has been exported
            $qti = $item->toXML();
            $this->assertFalse(empty($qti));

            //test if it's a valid QTI file
            $tmpFile = $this->createFile('', uniqid('qti_', true).'.xml');
            file_put_contents($tmpFile, $qti);
            $this->assertTrue(file_exists($tmpFile));

            $parserValidator = new taoQTI_models_classes_QTI_Parser($tmpFile);
            $parserValidator->validate();

            if(!$parserValidator->isValid()){
                $this->fail($parserValidator->displayErrors());
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

            $qtiParser = new taoQTI_models_classes_QTI_Parser($file);
            $item = $qtiParser->load();

            $this->assertTrue($qtiParser->isValid());
            $this->assertNotNull($item);
            $this->assertIsA($item, 'taoQTI_models_classes_QTI_Item');

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