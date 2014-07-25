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

/**
 * the qti TestModel
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQtiTest
 * @subpackage models_classes
 */
class taoQtiTest_models_classes_TestModel
	implements taoTests_models_classes_TestModel
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---
    const CONFIG_QTITEST_FOLDER = 'qtiTestFolder';

    // --- OPERATIONS ---
    /**
     * default constructor to ensure the implementation
     * can be instanciated
     */
    public function __construct() {
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::prepareContent()
     */
    public function prepareContent( core_kernel_classes_Resource $test, $items = array()) {
        $service = taoQtiTest_models_classes_QtiTestService::singleton();
        $service->saveQtiTest($test, $items);
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::deleteContent()
     */
    public function deleteContent( core_kernel_classes_Resource $test) {
        $service = taoQtiTest_models_classes_QtiTestService::singleton();
        $service->deleteContent($test);
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::getItems()
     */
    public function getItems( core_kernel_classes_Resource $test) {
    	$service = taoQtiTest_models_classes_QtiTestService::singleton();
        return $service->getItems($test);
    }

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onChangeTestLabel()
     */
    public function onChangeTestLabel( core_kernel_classes_Resource $test) {
    	// do nothing
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::getAuthoring()
     */
    public function getAuthoring( core_kernel_classes_Resource $test) {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
    	$widget = new Renderer($ext->getConstant('DIR_VIEWS').'templates'.DIRECTORY_SEPARATOR.'authoring_button.tpl');
		$widget->setData('uri', $test->getUri());
		$widget->setData('label', $test->getLabel());
    	return $widget->render();
    }
    
    public function getcompiler(core_kernel_classes_Resource $test) {
        return new taoQtiTest_models_classes_QtiTestCompiler($test);
    }
    
    public static function setQtiTestDirectory(core_kernel_file_File $folder) {
    	$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
    	$ext->setConfig(self::CONFIG_QTITEST_FOLDER, $folder->getUri());
    }
    
    public static function getQtiTestDirectory() {
    	
    	$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
        $uri = $ext->getConfig(self::CONFIG_QTITEST_FOLDER);
        if (empty($uri)) {
        	throw new common_Exception('No default repository defined for uploaded files storage.');
        }
		return new core_kernel_file_File($uri);
    }

    /**
     * Clone a QTI Test Resource.
     * 
     * @param core_kernel_classes_Resource $source The resource to be cloned.
     * @param core_kernel_classes_Resource $destination An existing resource to be filled as the clone of $source.
     */
    public function cloneContent( core_kernel_classes_Resource $source, core_kernel_classes_Resource $destination) {
        $contentProperty = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
        $existingFile = new core_kernel_file_File($source->getUniquePropertyValue($contentProperty)->getUri());
        $existingContent = $existingFile->getFileContent();
        
        $service = taoQtiTest_models_classes_QtiTestService::singleton();
        $newFile = $service->createContent($destination);
        $newFile->setContent($existingContent);
    }
}

?>