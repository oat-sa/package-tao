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
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @license GPLv2
 * @package tao
 *
 */

use oat\tao\helpers\translation\TranslationBundle;
use oat\tao\test\TaoPhpUnitTestRunner;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';


/**
 * Unit and Integration(fs) test {@link oat\tao\heplers\translation\TranslationBundle}
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @package tao
 */
class TranslationBundleTest extends TaoPhpUnitTestRunner {

    /**
     * A temporary directory to write translations bundles
     */
    private static $tmpDir;

    /**
     * Set up the temp directory
     */
    public static function setUpBeforeClass(){
        self::$tmpDir = sys_get_temp_dir() . '/phpunit-' . __CLASS__;
        if(!file_exists(self::$tmpDir)){
            mkdir(self::$tmpDir);
        }
    }
   

    
    /**
     * Removes the temporary directory
     */
    public static function tearDownAfterClass(){
        tao_helpers_File::delTree(self::$tmpDir);  
    }
    /**
     * Provides wrong constructor parameters
     * @return array() the data
     */     
    public function wrongConstructorProvider(){
        return array(
            array(true, array()),
            array('test', 12),
            array(null, null),
        );
    }   
 
    /**
     * Test constructor with wrong parameters
     * @param string $langCode
     * @param array $extensions
     * @dataProvider wrongConstructorProvider
     * @expectedException common_exception_InvalidArgumentType
     */
    public function testWrongConstructor($langCode, $extensions){
       new TranslationBundle($langCode, $extensions); 
    }
   
    /**
     * Provides data to test the bundle
     * @return array() the data
     */     
    public function bundleProvider(){
        return array(
            array('en-US', array(new \common_ext_Extension('tao'), new \common_ext_Extension('taoItems')), md5('en-US_tao-taoItems')),
            array('fr-FR', array(new \common_ext_Extension('tao'), new \common_ext_Extension('taoItems')), md5('fr-FR_tao-taoItems')),
        );
    }   
 
    /**
     * Test the bundle
     * @param string $langCode
     * @param array $extensions
     * @dataProvider bundleProvider
     */
    public function testBundle($langCode, $extensions, $expectedSerial){
       $bundle = new TranslationBundle($langCode, $extensions); 

       $serial = $bundle->getSerial();
       $this->assertTrue(is_string($serial));
       $this->assertEquals($expectedSerial, $serial);

       if(is_dir(self::$tmpDir)){
            $file = $bundle->generateTo(self::$tmpDir);
            $this->assertTrue(file_exists($file));

            $content = json_decode(file_get_contents($file), true);
            $this->assertTrue(is_array($content));
            $this->assertEquals($expectedSerial, $content['serial']);
        }
    }

}
