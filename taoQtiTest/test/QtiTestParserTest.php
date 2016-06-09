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
use \taoQtiTest_models_classes_ManifestParser;
use \taoQtiTest_models_classes_QtiTestCompiler;
use \core_kernel_classes_Resource;
use \core_kernel_classes_Property;
use \tao_models_classes_service_FileStorage;
use \common_report_Report;

/**
 * This test case focuses on testing the ManifestParser model.
 *
 * @author Aamir
 * @package taoQtiTest
 */
class QtiTestParserTest extends TaoPhpUnitTestRunner
{

    static public function dataDir()
    {
        return dirname(__FILE__) . '/data/';
    }

    static public function samplesDir()
    {
        return dirname(__FILE__) . '/samples/';
    }

    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
    }

    /**
     *
     * @return mixed|null
     */
    public function testManifestParserObject()
    {
        $objParser = new taoQtiTest_models_classes_ManifestParser($this->dataDir() . 'imsmanifest_mapping_1.xml');
        $this->assertNotNull($objParser);
        
        return $objParser;
    }

    /**
     * @depends testManifestParserObject
     * 
     * @param $objParser
     * @return void
     */
    public function testManifestParserValidate($objParser)
    {
        $this->assertTrue($objParser->validate());
    }

    /**
     * @depends testManifestParserObject
     * 
     * @param $objParser
     * @return void
     */
    public function testManifestParserGetResources($objParser)
    {
        $idResources = $objParser->getResources(null, taoQtiTest_models_classes_ManifestParser::FILTER_RESOURCE_IDENTIFIER);
        $this->assertEquals(4, count($idResources));
        
        $typeResources = $objParser->getResources('imsqti_test_xmlv2p1', taoQtiTest_models_classes_ManifestParser::FILTER_RESOURCE_TYPE);
        $this->assertEquals(1, count($typeResources));
        
        $typeResourcesDefault = $objParser->getResources('imsqti_test_xmlv2p1');
        $this->assertEquals(1, count($typeResourcesDefault));
    }

    /**
     * Initialize the compiler
     * 
     * @return \taoQtiTest_models_classes_QtiTestCompiler
     */
    public function testQtiTestCreateCompiler()
    {
        $content = new core_kernel_classes_Resource($this->dataDir() . 'qtitest.xml');
        
        $storage = tao_models_classes_service_FileStorage::singleton();
        
        $this->assertIsA($content, 'core_kernel_classes_Resource');
        $this->assertIsA($storage, 'tao_models_classes_service_FileStorage');
        
        $compiler = new taoQtiTest_models_classes_QtiTestCompiler($content, $storage);
        $this->assertIsA($compiler, 'taoQtiTest_models_classes_QtiTestCompiler');
        
        return $compiler;
    }

    
    
    /**
     * @depends testQtiTestCreateCompiler
     * 
     * @param \taoQtiTest_models_classes_QtiTestCompiler $compiler            
     * @return void
     */
    public function testQtiTextCompilerCompile($compiler)
    {
        $report = $compiler->compile();
        $this->assertEquals($report->getType(), common_report_Report::TYPE_ERROR);
        $serviceCall = $report->getData();
        $this->assertNull($serviceCall);
    }
}