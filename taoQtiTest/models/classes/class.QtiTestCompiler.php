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
 */

use qtism\data\QtiComponentIterator;
use qtism\data\storage\xml\XmlCompactAssessmentTestDocument;
use qtism\data\storage\xml\XmlAssessmentTestDocument;


/**
 * Compiles a QTI Test and related QTI Items.
 *
 * @access public
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @package taoQtiTest
 * @subpackage models_classes
 */
class taoQtiTest_models_classes_QtiTestCompiler extends tao_models_classes_Compiler
{
    /**
     * Compile a QTI Test and the related QTI Items.
     * 
     * @param core_kernel_file_File $destinationDirectory The directory where the compiled files must be put.
     * @return tao_models_classes_service_ServiceCall A ServiceCall object that represent the way to call the newly compiled test.
     * @throws tao_models_classes_CompilationFailedException If an error occurs during the compilation.
     */
    public function compile(core_kernel_file_File $destinationDirectory) {
        
        $test = $this->getResource();
        common_Logger::i('Compiling QTI test ' . $test->getLabel().' and the related QTI Items');
        
        // 1. Compile the test definition itself.
        $testContentProperty = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
        $testContent = new core_kernel_file_File($test->getUniquePropertyValue($testContentProperty)->getUri());
        $itemResolver = new taoQtiTest_helpers_ItemResolver('');
        
        $testContentPath = $testContent->getAbsolutePath();
        $originalDoc = new XmlAssessmentTestDocument('2.1');
        $originalDoc->load($testContentPath);
        common_Logger::t("QTI Test XML document located at '${testContentPath}' successfully loaded.");
        
        $compiledDoc = XmlCompactAssessmentTestDocument::createFromXmlAssessmentTestDocument($originalDoc, $itemResolver);
        common_Logger::t("QTI Test XML document successfuly transformed in a compact version.");
        
        // 2. Compile the items of the test.
        $iterator = new QtiComponentIterator($compiledDoc, array('assessmentItemRef'));
        $itemCount = 0;
        foreach ($iterator as $assessmentItemRef) {
            $itemToCompile = new core_kernel_classes_Resource($assessmentItemRef->getHref());
            $itemService = $this->getItemRunnerService($itemToCompile, $destinationDirectory);
            $inputValues = tao_models_classes_service_ServiceCallHelper::getInputValues($itemService, array());
            $assessmentItemRef->setHref($inputValues['itemUri'] . '|' . $inputValues['itemPath'] . '|' . $this->getResource()->getUri());
            $itemCount++;
            
            common_Logger::d("QTI Item successfuly compiled and registered as a service call in the QTI Test Definition.");
        }
        
        if ($itemCount === 0) {
            $msg = "Cannot compile a QTI Test without any QTI Items.";
            $code = taoQtiTest_models_classes_QtiTestCompilationFailedException::NO_ITEMS;
            throw new taoQtiTest_models_classes_QtiTestCompilationFailedException($msg, $test, $code);
        }
        
        $compiledDocPath = $destinationDirectory->getAbsolutePath() . DIRECTORY_SEPARATOR . 'compact-test.xml';
        $compiledDoc->save($compiledDocPath);
        
        $compiledFile = $destinationDirectory->getFileSystem()->createFile('compact-test.xml', $destinationDirectory->getRelativePath() . DIRECTORY_SEPARATOR);
        common_Logger::d("QTI Item Compilation file registered at '" . $compiledFile->getAbsolutePath() . '".');
        
        // 3. Build the service call.
        $service = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(INSTANCE_QTITEST_TESTRUNNERSERVICE));
        $param = new tao_models_classes_service_ConstantParameter(
                        // Test Definition URI passed to the QtiTestRunner service.
                        new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_QTITEST_TESTDEFINITION),
                        $test
        );
        $service->addInParameter($param);
        
        $param = new tao_models_classes_service_ConstantParameter(
                        // Test Compilation URI passed to the QtiTestRunner service.
                        new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_QTITEST_TESTCOMPILATION),
                        $compiledFile
        );
        $service->addInParameter($param);
        
        common_Logger::t("QTI Test successfuly compiled.");
        
        return $service;
    }
    
    
    /**
     * Get the service call for $item.
     * 
     * @param core_kernel_classes_Resource $item
     * @param core_kernel_file_File $destinationDirectory
     * @return tao_models_classes_service_ServiceCall
     */
    protected function getItemRunnerService(core_kernel_classes_Resource $item, core_kernel_file_File $destinationDirectory)
    {   
        $itemDirectory = $this->createSubDirectory($destinationDirectory, $item);
        
        $compiler = taoItems_models_classes_ItemsService::singleton()->getCompiler($item);
        $callService = $compiler->compile($itemDirectory);
        return $callService;
    }
    
}