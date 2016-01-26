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
use taoQtiTest_models_classes_import_TestImport;
use \common_report_Report;
use \core_kernel_classes_Class;

/**
 * This test case focuses on testing the import_TestImport model.
 *
 * @author Aamir
 * @package taoQtiTest
 */
class QtiTestImportTest extends TaoPhpUnitTestRunner
{

    private $dataDir = '';
    
    private $tmpDir = '';

    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        $this->dataDir = dirname(__FILE__) . '/data/';
        
        $this->tmpDir = sys_get_temp_dir() . '/' ;
    }

    /**
     * verify main class
     *
     * @return \taoQtiTest_models_classes_import_TestImport
     */
    public function testInitImport()
    {
        $testImport = new taoQtiTest_models_classes_import_TestImport();
        $this->assertInstanceOf('taoQtiTest_models_classes_import_TestImport', $testImport);

        return $testImport;
    }

    /**
	 * test import form create
	 *
     * @depends testInitImport
     * @param  \taoQtiTest_models_classes_import_TestImport $testImport
     * @return \tao_helpers_form_Form
     */
    public function testImportFormCreate($testImport)
    {
        $form = $testImport->getForm();

        $this->assertInstanceOf('tao_helpers_form_Form', $form);
        $this->assertInstanceOf('tao_helpers_form_xhtml_Form', $form);

        return $form;
    }

    /**
	 * test import form validators
	 *
     * @depends testImportFormCreate
     * @param  \tao_helpers_form_Form $form
     * @return void
     */
    public function testImportFormValid($form)
    {
        $this->assertFalse($form->isValid());
    }

    /**
	 * test import form values
	 *
     * @depends testImportFormCreate
     * @param  \tao_helpers_form_Form $form
     * @return void
     */
    public function testImportFormValues($form)
    {
        $this->assertEquals(2, count($form->getElements()));

        $elmSource = $form->getElement('source');
        $this->assertInstanceOf('tao_helpers_form_FormElement', $elmSource);

        $elmSource->setValue(array(
            'uploaded_file' => $this->dataDir . 'qtitest.xml'
        ));
        $this->assertFalse($form->isValid());

        copy($this->dataDir . 'qti_package.zip', $this->tmpDir . 'qti_package_copy.zip');
        $elmSource->setValue(array(
            'uploaded_file' => $this->tmpDir . 'qti_package_copy.zip'
        ));

        $elmSentQti = $form->getElement('import_sent_qti');
        $this->assertInstanceOf('tao_helpers_form_FormElement', $elmSentQti);
        $elmSentQti->setValue(1);
    }

    /**
	 * test import form validate
	 *
     * @depends testImportFormCreate
     * @param  \tao_helpers_form_Form $form
     * @return void
     */
    public function testImportFormValidate($form)
    {
        $source = $form->getElement('source')->getRawValue();
        $this->assertArrayHasKey('uploaded_file', $source);

        $value = $form->getElement('import_sent_qti')->getRawValue();
        $this->assertEquals(1, $value);
    }

    

    
    /**
	 * test import
	 *
     * @depends testInitImport
     * @depends testImportFormCreate
     * @param  \taoQtiTest_models_classes_import_TestImport $testImport
     * @param  \tao_helpers_form_Form                       $form
     * @return void
     */
    public function testImportFormSubmit($testImport, $form)
    {
        $class = new core_kernel_classes_Class(TAO_TEST_CLASS);
        
        $report = $testImport->import($class, $form);
        $this->assertInstanceOf('common_report_Report', $report);

        // As the QTI Package has no test into it, the report has to be TYPE_ERROR.
        // 'qti_package.zip'. @todo have a TYPE_SUCCESS case.
        $this->assertEquals($report->getType(), common_report_Report::TYPE_ERROR);
    }

}
