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

/**
 * the wfEngine TestModel
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoWfTest
 
 */
class taoWfTest_models_classes_WfTestModel
	implements taoTests_models_classes_TestModel
{
    
	/**
     * (non-PHPdoc)
	 * @see taoTests_models_classes_TestModel::__construct()
	 */
	public function __construct() {
	    common_ext_ExtensionsManager::singleton()->getExtensionById('taoWfTest'); // loads the extension
	}
	
	/**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::getAuthoring()
     */
    public function getAuthoring( core_kernel_classes_Resource $test) {
    	
    	$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoWfTest');
    	$testService = taoTests_models_classes_TestsService::singleton();

    	$itemSequence = array();
		$itemUris = array();
		$i = 1;
		foreach($testService->getTestItems($test) as $item){
			$itemUris[] = $item->getUri();
			$itemSequence[$i] = array(
				'uri' 	=> tao_helpers_Uri::encode($item->getUri()),
				'label' => $item->getLabel()
			);
			$i++;
		}

		// data for item sequence, terrible solution
		// @todo implement an ajax request for labels or pass from tree to sequence
		$allItems = array();
		foreach($testService->getAllItems() as $itemUri => $itemLabel){
			$allItems['item_'.tao_helpers_Uri::encode($itemUri)] = $itemLabel;
		}
		
    	$widget = new Renderer($ext->getConstant('DIR_VIEWS').'templates'.DIRECTORY_SEPARATOR.'authoring.tpl');
		$widget->setData('uri', $test->getUri());
    	$widget->setData('allItems', json_encode($allItems));
		$widget->setData('itemSequence', $itemSequence);
		
		// data for generis tree form
		$widget->setData('relatedItems', json_encode(tao_helpers_Uri::encodeArray($itemUris)));
		$openNodes = tao_models_classes_GenerisTreeFactory::getNodesToOpen($itemUris, new core_kernel_classes_Class(TAO_ITEM_CLASS));
		$widget->setData('itemRootNode', TAO_ITEM_CLASS);
		$widget->setData('itemOpenNodes', $openNodes);
		$widget->setData('saveUrl', _url('saveItems', 'Authoring', 'taoWfTest'));
		return $widget->render();
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function prepareContent( core_kernel_classes_Resource $test, $items = array()) {
    	taoWfTest_models_classes_WfTestService::singleton()->createTestProcess($test);
    	taoWfTest_models_classes_WfTestService::singleton()->setTestItems($test, $items);
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function deleteContent( core_kernel_classes_Resource $test) {
    	$content = $test->getOnePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
    	if (!is_null($content)) {
    		$content->delete();
    		$test->removePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP), $content);
    	}
    }
    
    public function getItems( core_kernel_classes_Resource $test) {
    	return taoWfTest_models_classes_WfTestService::singleton()->getTestItems($test);
    }

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::cloneContent()
     */
    public function cloneContent( core_kernel_classes_Resource $source, core_kernel_classes_Resource $destination) {

		//clone the process:
		$propInstanceContent = new core_kernel_classes_Property(TEST_TESTCONTENT_PROP);
		try{
			$process = $source->getUniquePropertyValue($propInstanceContent);
			$processCloner = new wfAuthoring_models_classes_ProcessCloner();
			$processClone = $processCloner->cloneProcess($process);
			$destination->editPropertyValues($propInstanceContent, $processClone->getUri());
		} catch(Exception $e) {
			throw new Exception("the test process cannot be found");
		}
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onChangeTestLabel()
     */
    public function onChangeTestLabel( core_kernel_classes_Resource $test) {
    	$process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
    	$process->setLabel("Process ".$test->getLabel());
    }
    
    public function getCompilerClass() {
        return 'taoWfTest_models_classes_WfTestCompiler';
    }
}