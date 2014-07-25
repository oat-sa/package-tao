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
 * the wfEngine DeliveryModel
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoWfTest
 
 */
class taoWfDelivery_models_classes_WfContentModel
	implements taoDelivery_models_classes_ContentModel
{
    /**
     * The workflow content extension
     * 
     * @var common_ext_Extension
     */
    private $extension;
    
    public function __construct() {
        $this->extension = common_ext_ExtensionsManager::singleton()->getExtensionById('taoWfDelivery');
	}
	
	/**
	 * Returns the class of this content model
	 * 
	 * @return core_kernel_classes_Class
	 */
	public function getClass() {
	    return new core_kernel_classes_Class(CLASS_WORKFLOW_DELIVERYCONTENT);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see taoDelivery_models_classes_ContentModel::getAuthoring()
	 */
    public function getAuthoring( core_kernel_classes_Resource $content) {
        $widget = new Renderer($this->extension->getConstant('DIR_VIEWS').'templates'.DIRECTORY_SEPARATOR.'authoring.tpl');
        $widget->setData('processUri', $this->getProcessDefinition($content)->getUri());
        $widget->setData('label', __('Authoring'));
        return $widget->render();
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function createContent($tests = array()) {
        $content = $this->getClass()->createInstance();
        $processClass = new core_kernel_classes_Class(CLASS_PROCESS);
        $processInstance = $processClass->createInstance();
        $processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_ACL_MODE), INSTANCE_ACL_ROLE);
        $processInstance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_INIT_RESTRICTED_ROLE), INSTANCE_ROLE_DELIVERY);
        $content->editPropertyValues(new core_kernel_classes_Property(PROPERTY_DELIVERYCONTENT_PROCESS), $processInstance);
        
        return $content;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function delete( core_kernel_classes_Resource $content) {
        $process = $this->getProcessDefinition($content);
        $success = wfAuthoring_models_classes_ProcessService::singleton()->deleteProcess($process);
    	if ($success) {
    		$success = $content->delete();
    	}
    	return $success;
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_ContentModel::cloneContent()
     */
    public function cloneContent(core_kernel_classes_Resource $content) {
        $clone = $content->duplicate(array(PROPERTY_DELIVERYCONTENT_PROCESS));
        $process = $this->getProcessDefinition($content);
		common_Logger::i('Process '.$process);
        $processCloner = new wfAuthoring_models_classes_ProcessCloner();
		$processClone = $processCloner->cloneProcess($process);
		common_Logger::i('Clone '.$processClone);
		$clone->editPropertyValues(new core_kernel_classes_Property(PROPERTY_DELIVERYCONTENT_PROCESS), $processClone);
        return $clone;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onChangeTestLabel()
     */
    public function onChangeDeliveryLabel( core_kernel_classes_Resource $delivery) {
        $content = taoDelivery_models_classes_DeliveryTemplateService::singleton()->getContent($delivery);
        if (is_null($content)) {
            throw new common_exception_Error(__FUNCTION__.' called on a delivery('.$delivery->getUri().') without content');
        }
        $process = $this->getProcessDefinition($content);
    	$process->setLabel("Process ".$delivery->getLabel());
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_ContentModel::getCompilerClass()
     */
    public function getCompilerClass() {
        return "taoWfDelivery_models_classes_DeliveryCompiler";
    }
    
    protected function getProcessDefinition(core_kernel_classes_Resource $content) {
        return $content->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_DELIVERYCONTENT_PROCESS));
    }

}