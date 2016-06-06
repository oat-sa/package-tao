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
use \taoQtiTest_models_classes_QtiTestService;
use \taoQtiTest_models_classes_export_TestExport;
use \tao_helpers_Uri;
use \ZipArchive;
use \taoQtiTest_models_classes_export_QtiTestExporter;
use \taoQtiTest_helpers_Utils;

/**
 * This test case focuses on testing the export_TestExport and export_QtiTestExporter models.
 *
 * @author Aamir
 * @package taoQtiTest
 */
class QtiTestExporterTest extends TaoPhpUnitTestRunner
{

    private $dataDir = '';
    private $outputDir;
    

    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        $this->testService = taoQtiTest_models_classes_QtiTestService::singleton();
        $this->dataDir = dirname(__FILE__) . '/data/';
        
        
        $this->outputDir = sys_get_temp_dir() . '/' ;

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

        $type = current($qtiTest->getTypes());
        $this->assertEquals(TAO_TEST_CLASS, $type->getUri());

        return $qtiTest;
    }

    /**
     * verify main class
     *
     * @return \taoQtiTest_models_classes_export_TestExport
     */
    public function testInitExport()
    {
        $testExport = new taoQtiTest_models_classes_export_TestExport();
        $this->assertInstanceOf('taoQtiTest_models_classes_export_TestExport', $testExport);

        return $testExport;
    }

    /**
	 * test export form create
	 *
     * @depends testInitExport
     * @depends testCreateInstance
     * @param  \taoQtiTest_models_classes_export_TestExport $testExport
     * @param  \core_kernel_classes_Resource                $qtiTest
     * @return \tao_helpers_form_Form
     */
    public function testExportFormCreate($testExport, $qtiTest)
    {
        $form = $testExport->getExportForm($qtiTest);
        $this->assertInstanceOf('tao_helpers_form_Form', $form);
        $this->assertInstanceOf('tao_helpers_form_xhtml_Form', $form);

        return $form;
    }

    /**
	 * test export form validators
	 *
     * @depends testExportFormCreate
     * @param  \tao_helpers_form_Form $form
     * @return void
     */
    public function testExportFormValid($form)
    {
        $this->assertFalse($form->isValid());
    }

    /**
	 * test export form values
	 *
     * @depends testExportFormCreate
	 * @depends testCreateInstance
     * @param \tao_helpers_form_Form $form
	 * @param  \core_kernel_classes_Resource $qtiTest
     * @return void
     */
    public function testExportFormValues($form, $qtiTest)
    {
        $this->assertEquals(2, count($form->getElements()));

        $elmSource = $form->getElement('filename');
        $this->assertInstanceOf('tao_helpers_form_FormElement', $elmSource);
        $elmSource->setValue('qti_unit_test');

        $elmInstance = $form->getElement('instances');
        $this->assertInstanceOf('tao_helpers_form_FormElement', $elmInstance);

        $uri = tao_helpers_Uri::encode($qtiTest->getUri());
        $elmInstance->setOptions(array(
            $uri => $qtiTest->getLabel()
        ));
    }

    /**
	 * test export form validate
	 *
     * @depends testExportFormCreate
	 * @depends testCreateInstance
     * @param \tao_helpers_form_Form $form
	 * @param  \core_kernel_classes_Resource $qtiTest
     * @return void
     */
    public function testExportFormValidate($form, $qtiTest)
    {
        $filename = $form->getElement('filename')->getRawValue();
        $this->assertEquals('qti_unit_test', $filename);

        $uri = tao_helpers_Uri::encode($qtiTest->getUri());
        $instances = $form->getElement('instances')->getRawValue();

        $this->assertTrue(in_array($uri, $instances));
    }

    /**
	 * test export
	 *
     * @depends testInitExport
     * @depends testExportFormCreate
	 * @depends testCreateInstance
     * @param \taoQtiTest_models_classes_export_TestExport $testExport
     * @param \tao_helpers_form_Form                       $form
	 * @param  \core_kernel_classes_Resource $qtiTest
     * @return void
     */
    public function testExportFormSubmit($testExport, $form)
    {
        $report = $testExport->export($form->getValues(), $this->outputDir);

        $this->assertInstanceOf('common_report_Report', $report);
        $file = $report->getData();
        
        $this->assertInternalType('string', $file);
        $this->assertFileExists($file);
        $this->assertStringStartsWith($this->outputDir, $file);

        $this->assertContains('qti_unit_test', $file);
        unlink($file);
    }

    /**
	 * test QtiTestExporter alone
	 *
	 * @depends testCreateInstance
	 * @param  \core_kernel_classes_Resource $qtiTest
     * @return void
     */
    public function testQtiTestExporter($qtiTest)
    {
        $file = $this->outputDir . 'qti_unit_test.zip';

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($file, ZipArchive::CREATE));

        $qtiTestExporter = new taoQtiTest_models_classes_export_QtiTestExporter(
            $qtiTest, $zip, taoQtiTest_helpers_Utils::emptyImsManifest()
        );
        $qtiTestExporter->export();
        $zip->close();

        $this->assertFileExists($file);
        unlink($file);
    }

}
