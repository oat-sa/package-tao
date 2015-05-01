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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
namespace oat\taoQtiItem\test;

use common_ext_ExtensionsManager;
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoQtiItem\model\qti\Service;
use oat\taoQtiItem\model\qti\Parser;
use oat\taoQtiItem\model\qti\JsonLoader;
//include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoQTI

 */
class QtiParsingTest extends TaoPhpUnitTestRunner {

	protected $qtiService;

	/**
	 * tests initialization
	 */
	public function setUp(){
		TaoPhpUnitTestRunner::initTest();
		$this->qtiService = Service::singleton();
	}

	/**
	 * test qti file parsing: validation and loading in a non-persistant context
	 */
	public function testFileParsingQti2p1(){

		//check if wrong files are not validated correctly
		foreach(glob(dirname(__FILE__).'/samples/wrong/*.*') as $file){
			$qtiParser = new Parser($file);
			$qtiParser->validate();

			$this->assertFalse($qtiParser->isValid());
			$this->assertTrue(count($qtiParser->getErrors()) > 0);
			$this->assertTrue(strlen($qtiParser->displayErrors()) > 0);
		}

        $files = array_merge(
                glob(dirname(__FILE__).'/samples/xml/qtiv2p1/*.xml'), glob(dirname(__FILE__).'/samples/xml/qtiv2p1/rubricBlock/*.xml')
        );
		//check if samples are loaded
		foreach($files as $file){

			$qtiParser = new Parser($file);
			$qtiParser->validate();

            if(!$qtiParser->isValid()){
                echo $qtiParser->displayErrors();
            }

			$this->assertTrue($qtiParser->isValid());

			$item = $qtiParser->load();

            $this->assertInstanceOf('\\oat\\taoQtiItem\\model\\qti\\Item', $item);

		}

	}
    /**
     * test if a correctResponse with CDATA works
     * @author Thibault Milan <thibault.milan@vesperiagroup.com>
     */
    public function testFileParsingCDATA(){
        $file = dirname(__FILE__).'/samples/xml/cdata/item.xml';

        $qtiParser = new Parser($file);
        $qtiParser->validate();

        if(!$qtiParser->isValid()){
            $this->fail($qtiParser->displayErrors());
        }

        $this->assertTrue($qtiParser->isValid());

        $item = $qtiParser->load();

        $this->assertInstanceOf('\\oat\\taoQtiItem\\model\\qti\\Item', $item);

        $responses = $item->getResponses();
        foreach ($responses as $response) {
            $correctResponses = $response->getCorrectResponses();
            foreach ($correctResponses as $correctResponse) {
                $this->assertFalse(strstr($correctResponse,"<![CDATA["),"<![CDATA[ (CDATA opening tag) detected.");
                $this->assertFalse(strstr($correctResponse,"]]>"),"]]> (CDATA closing tag) detected");
            }
        }
    }

    public function testFileParsingQti2p0(){
        $basePath = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem')->getDir();
        $qtiv2p1xsd = $basePath.'model/qti/data/qtiv2p0/imsqti_v2p0.xsd';

        foreach(glob(dirname(__FILE__).'/samples/xml/qtiv2p0/*.xml') as $file){

            $qtiParser = new Parser($file);
            $qtiParser->validate($qtiv2p1xsd);
            if(!$qtiParser->isValid()){
                echo $qtiParser->displayErrors();
            }

            $this->assertTrue($qtiParser->isValid());

            $item = $qtiParser->load();

            $this->assertInstanceOf('\\oat\\taoQtiItem\\model\\qti\\Item', $item);

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
                $this->fail($parserValidator->displayErrors());
            }
        }
    }

    public function testFileParsingApipv1p0(){

        $basePath = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem')->getDir();

        foreach(glob(dirname(__FILE__).'/samples/xml/apipv1p0/*.xml') as $file){

            $qtiParser = new Parser($file);
            $qtiParser->validate($basePath.'model/qti/data/apipv1p0/Core_Level/Package/apipv1p0_qtiitemv2p1_v1p0.xsd');
            if(!$qtiParser->isValid()){
                echo $qtiParser->displayErrors();
            }

            $this->assertTrue($qtiParser->isValid());

            $item = $qtiParser->load();

            $this->assertInstanceOf('\\oat\\taoQtiItem\\model\\qti\\Item', $item);

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
                $this->fail($parserValidator->displayErrors());
            }
        }
    }

    public function testJsonLoading(){

        foreach(glob(dirname(__FILE__).'/samples/json/*.json') as $file){

            if(strpos($file, 'ALL') !== false){continue;}

            $json = json_decode(file_get_contents($file), true);
            $jsonLoader = new JsonLoader($json['full']);
            $item = $jsonLoader->load();
            $this->assertInstanceOf('\\oat\\taoQtiItem\\model\\qti\\Item', $item);
        }
    }

	/**
	 * test the building an QTI_Item object from it's XML definition
	 */
	public function testBuilding(){

		$qtiParser = new Parser(dirname(__FILE__).'/samples/xml/qtiv2p1/choice.xml');
		$item = $qtiParser->load();

		$this->assertTrue($qtiParser->isValid());
		$this->assertNotNull($item);
		$this->assertInstanceOf('\\oat\\taoQtiItem\\model\\qti\\Item', $item);

		$this->assertEquals(count($item->getInteractions()),1, 'nr of interactions in choice.xml differs from 1');

		$this->assertFalse(strlen((string) $item->getBody()) == 0, 'itembody empty');
		foreach($item->getInteractions() as $interaction){
			$this->assertInstanceOf('\\oat\\taoQtiItem\\model\\qti\\interaction\\ChoiceInteraction',$interaction);

			foreach($interaction->getChoices() as $choice){
				$this->assertInstanceOf('\\oat\\taoQtiItem\\model\\qti\\choice\\Choice',$choice);
			}
		}

	}

    /**
	 * test qti file parsing: validation and loading in a non-persistant context
	 */
	public function testFileParsingQtiPci(){

        $files = glob(dirname(__FILE__).'/samples/xml/qtiv2p1/pci/*.xml');

		//check if samples are loaded
		foreach($files as $file){
			$qtiParser = new Parser($file);

            $qtiParser->validate();
            if(!$qtiParser->isValid()){
                echo $qtiParser->displayErrors();
            }

			$item = $qtiParser->load();
			$this->assertInstanceOf('\\oat\\taoQtiItem\\model\\qti\\Item',$item);
		}

	}

	public function testFileParsingQtiPic(){

        $files = glob(dirname(__FILE__).'/samples/xml/qtiv2p1/pic/*.xml');

		//check if samples are loaded
		foreach($files as $file){
			$qtiParser = new Parser($file);

            $qtiParser->validate();
            if(!$qtiParser->isValid()){
                echo $qtiParser->displayErrors();
            }

			$item = $qtiParser->load();
			$this->assertInstanceOf('\\oat\\taoQtiItem\\model\\qti\\Item',$item);

		}
	}

}
