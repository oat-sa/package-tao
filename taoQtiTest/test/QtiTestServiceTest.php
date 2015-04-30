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
namespace oat\taoQtiTest\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use \taoTests_models_classes_TestsService;
use \taoQtiTest_models_classes_QtiTestService;
use \core_kernel_classes_Property;
use \common_ext_ExtensionsManager;
use \taoQtiTest_models_classes_TestModel;
use \common_report_Report;


/**
 * This test case focuses on testing the ManifestParser model.
 *
 * @author Aamir
 * @package taoQtiTest
 */
class QtiTestServiceTest extends TaoPhpUnitTestRunner
{

    /**
     *
     * @var taoQtiTest_models_classes_QtiTestService
     */
    protected $testService = null;

    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
        
        $this->testService = taoQtiTest_models_classes_QtiTestService::singleton();
    }

    /**
     * verify main class
     * 
     * @return void
     */
    public function testService()
    {
        $this->assertIsA($this->testService, 'taoQtiTest_models_classes_QtiTestService');
    }

    /**
     * create qtitest instance
     * 
     * @return \core_kernel_classes_Resource
     */
    public function testCreateInstance()
    {
        $qtiTest = $this->testService->createInstance($this->testService->getRootclass(), 'UnitTestQtiItem');
        $this->assertInstanceOf('core_kernel_classes_Resource', $qtiTest);
        
        $this->assertTrue($qtiTest->isInstanceOf(new \core_kernel_classes_Class(TAO_TEST_CLASS)));
        return $qtiTest;
    }

    /**
     * verify that the test exists
     * @depends testCreateInstance
     * 
     * @param $qtiTest
     * @return void
     */
    public function testQtiTestExists($qtiTest)
    {
        $this->assertTrue($qtiTest->exists());
    }
    
    /**
     * verify that the test can be cloned
     * @depends testCreateInstance
     *
     * @param $qtiTest
     * @return void
     */
    public function testCloneInstance($qtiTest)
    {
        $clone = $this->testService->cloneInstance($qtiTest, $this->testService->getRootclass());
        $this->assertInstanceOf('\core_kernel_classes_Resource', $clone);
        $this->assertTrue($clone->exists());
        
        return $clone;
    }
    
    /**
     * Test clone content
     * @depends testCreateInstance
     * @depends testCloneInstance
     *
     * @param $clone
     */
    public function testCloneContent($qtiTest, $clone)
    {
        $origPath = $this->testService->getTestFile($qtiTest)->getAbsolutePath();
        $clonePath = $this->testService->getTestFile($clone)->getAbsolutePath();
        
        $this->assertFileExists($origPath);
        $this->assertFileExists($clonePath);
    
        $this->assertNotEquals($origPath, $clonePath);
        $this->assertFileEquals($origPath, $clonePath);
    }
        
    /**
     * Delete the qtiTest clone
     * @depends testCloneInstance
     *
     * @param $clone
     */
    public function testCloneInstanceDelete($clone)
    {
        $this->testService->deleteTest($clone);
        $this->assertFalse($clone->exists());
    }

    /**
     * Delete test
     * @depends testCreateInstance
     * 
     * @param  $qtiTest
     */
    public function testDeleteInstance($qtiTest)
    {
        $this->testService->deleteTest($qtiTest);
        $this->assertFalse($qtiTest->exists());
    }


    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testImportMultipleTests()
    {
        $datadir = dirname(__FILE__) . '/data/';
        $rootclass = $this->testService->getRootclass();
        $report = $this->testService->importMultipleTests($rootclass,$datadir.'unitqtitest.zip');
        $this->assertInstanceOf('common_report_Report', $report);      
        $this->assertEquals($report->getType(), common_report_Report::TYPE_SUCCESS);
        $testService = taoTests_models_classes_TestsService::singleton();
        foreach ($report as $rep){
            $result = $rep->getData();
           
            $this->assertInstanceOf('core_kernel_classes_Class', $result->itemClass);
            $this->assertInstanceOf('core_kernel_classes_Resource', $result->rdfsResource);
            foreach ($result->items as $items){
                $this->assertInstanceOf('core_kernel_classes_Resource', $items);
                $type = current($items->getTypes());
                $this->assertInstanceOf('core_kernel_classes_Resource', $type);
                
                $this->assertEquals($result->itemClass->getUri(),$type->getUri());
                $expectedLabel = array('Unattended Luggage','Associate Things');
                $this->assertTrue(in_array($items->getLabel(),$expectedLabel));
            }
            $testService->deleteTest($result->rdfsResource);
            
        }
        
    }
    
    /**
     * Verify that test attribute value in xml file will be properly encoded
     * (<b>&amp;</b>, <b>&lt;</b> and <b>&quot;</b> symbols must be encoded)
     * 
     * @author Aleh Hutnikau, hutnikau@1pt.com
     */
    public function testCreateContent()
    {
        $attrValue = '"A & B < C"';
        
        $qtiTest = $this->testService->createInstance($this->testService->getRootclass(), 'UnitTestQtiItem');
        $qtiTest->setLabel($attrValue);
        $this->testService->createContent($qtiTest);
        $xmlFilePath = $this->testService->getDocPath($qtiTest);
        $this->assertTrue(file_exists($xmlFilePath));
        
        $doc = new \DOMDocument();
        
        $this->assertTrue($doc->load($xmlFilePath));
        $this->assertEquals($attrValue, $doc->documentElement->getAttribute('title'));
        
        $this->testService->deleteTest($qtiTest);
        $this->assertFalse($qtiTest->exists());
    }
}