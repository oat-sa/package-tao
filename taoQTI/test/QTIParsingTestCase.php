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

require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoQTI
 * @subpackage test
 */
class QTIParsingTestCase extends TestCasePrototype {
	
	protected $qtiService;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoTestRunner::initTest();
		$this->qtiService = taoQTI_models_classes_QTI_Service::singleton();
	}
	
	/**
	 * test qti file parsing: validation and loading in a non-persistant context
	 */
	public function testFileParsingQti2p1(){
		
		//check if wrong files are not validated correctly
		foreach(glob(dirname(__FILE__).'/samples/wrong/*.*') as $file){
			$qtiParser = new taoQTI_models_classes_QTI_Parser($file);
			$qtiParser->validate();
			
			$this->assertFalse($qtiParser->isValid());
			$this->assertTrue(count($qtiParser->getErrors()) > 0);
			$this->assertTrue(strlen($qtiParser->displayErrors()) > 0);
		}
		
		//check if samples are loaded 
		foreach(glob(dirname(__FILE__).'/samples/xml/qtiv2p1/*.xml') as $file){
            
			$qtiParser = new taoQTI_models_classes_QTI_Parser($file);
			$qtiParser->validate();
			
            if(!$qtiParser->isValid()){
                echo $qtiParser->displayErrors();
            }
			
			$this->assertTrue($qtiParser->isValid());
			
			$item = $qtiParser->load();
			
			$this->assertIsA($item, 'taoQTI_models_classes_QTI_Item');
            
		}
        
	}
	
    public function testFileParsingQti2p0(){
        
        foreach(glob(dirname(__FILE__).'/samples/xml/qtiv2p0/*.xml') as $file){
            
            $qtiParser = new taoQTI_models_classes_QTI_Parser($file);
            $qtiv2p1xsd = BASE_PATH.'models/classes/QTI/data/qtiv2p0/imsqti_v2p0.xsd';
            $qtiParser->validate($qtiv2p1xsd);
            if(!$qtiParser->isValid()){
                echo $qtiParser->displayErrors();
            }

            $this->assertTrue($qtiParser->isValid());
            
            $item = $qtiParser->load();

            $this->assertIsA($item, 'taoQTI_models_classes_QTI_Item');
            
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
        }
    }
    
    public function testJsonLoading(){
        foreach(glob(dirname(__FILE__).'/samples/json/*.json') as $file){
            
            if(strpos($file, 'ALL') !== false){continue;}
            
            $jsonLoader = new taoQTI_models_classes_QTI_JsonLoader($file);
            $item = $jsonLoader->load();
            $this->assertIsA($item, 'taoQTI_models_classes_QTI_Item');
        }
    }
    
	/**
	 * test the building an QTI_Item object from it's XML definition
	 */
	public function testBuilding(){
		
		$qtiParser = new taoQTI_models_classes_QTI_Parser(dirname(__FILE__).'/samples/xml/qtiv2p1/choice.xml');
		$item = $qtiParser->load();
		
		$this->assertTrue($qtiParser->isValid());
		$this->assertNotNull($item);
		$this->assertIsA($item, 'taoQTI_models_classes_QTI_Item');
		
		$this->assertEqual(count($item->getInteractions()),1, 'nr of interactions in choice.xml differs from 1');
		
		$this->assertFalse(strlen((string) $item->getBody()) == 0, 'itembody empty');
		foreach($item->getInteractions() as $interaction){
			$this->assertIsA($interaction, 'taoQTI_models_classes_QTI_interaction_ChoiceInteraction');
			
			foreach($interaction->getChoices() as $choice){
				$this->assertIsA($choice, 'taoQTI_models_classes_QTI_choice_Choice');
			}
		}
	
	}
	
}