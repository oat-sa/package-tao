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

use \common_ext_ExtensionsManager;
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoQtiItem\model\qti\Service;
use oat\taoQtiItem\model\qti\PackageParser;
use oat\taoQtiItem\model\qti\ManifestParser;
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoQTI
 
 */
class QtiPackageParsingTest extends TaoPhpUnitTestRunner
{

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
    public function testFileParsing(){

        //check if wrong packages are not validated correctly
        foreach(glob(dirname(__FILE__).'/samples/parser/wrong/*.zip') as $file){

            $qtiParser = new PackageParser($file);

            $qtiParser->validate();

            $this->assertFalse($qtiParser->isValid(), 'failed with: '. $file);
            $this->assertTrue(count($qtiParser->getErrors()) > 0);
            $this->assertTrue(strlen($qtiParser->displayErrors()) > 0);
        }

        //check if package samples are valid
        foreach(glob(dirname(__FILE__).'/samples/package/QTI/*.zip') as $file){
            $qtiParser = new PackageParser($file);
            $qtiParser->validate();

            if(!$qtiParser->isValid())
                echo $qtiParser->displayErrors();

            $this->assertTrue($qtiParser->isValid());
        }


        //check if wrong manifest files are not validated correctly
        foreach(glob(dirname(__FILE__).'/samples/package/wrong/*.xml') as $file){

            $qtiParser = new ManifestParser($file);

            $qtiParser->validate();

            $this->assertFalse($qtiParser->isValid());
            $this->assertTrue(count($qtiParser->getErrors()) > 0);
            $this->assertTrue(strlen($qtiParser->displayErrors()) > 0);
        }

        //check if manifest samples are valid
        $basePath = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem')->getDir();
        foreach(glob(dirname(__FILE__).'/samples/package/*.xml') as $file){

            $qtiParser = new ManifestParser($file);

            $schema = '';
            $fileContent = file_get_contents($file);
            if(strpos($fileContent, 'xmlns="http://www.imsglobal.org/xsd/apip/apipv1p0/imscp_v1p1"')){
                $schema = $basePath.'model/qti/data/apipv1p0/Core_Level/Package/apipv1p0_imscpv1p2_v1p0.xsd';
            }

            $qtiParser->validate($schema);

            if(!$qtiParser->isValid()){
                echo $qtiParser->displayErrors();
            }

            $this->assertTrue($qtiParser->isValid());
        }
    }

}