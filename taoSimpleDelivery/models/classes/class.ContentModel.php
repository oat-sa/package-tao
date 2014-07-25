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
 * the simplest delivery model representing a single test
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 
 */
class taoSimpleDelivery_models_classes_ContentModel implements taoDelivery_models_classes_ContentModel
{

    /**
     * The simple delivery content extension
     *
     * @var common_ext_Extension
     */
    private $extension;

    public function __construct()
    {
        // ensures the constants are loaded
        $this->extension = common_ext_ExtensionsManager::singleton()->getExtensionById('taoSimpleDelivery');
    }

    public function getClass()
    {
        return new core_kernel_classes_Class(CLASS_SIMPLE_DELIVERYCONTENT);
    }

    /**
     * (non-PHPdoc)
     * 
     * @see taoTests_models_classes_TestModel::getAuthoring()
     */
    public function getAuthoring(core_kernel_classes_Resource $content)
    {
        common_Logger::i('Generating form for delivery content ' . $content->getUri());
        $widget = new Renderer($this->extension->getConstant('DIR_VIEWS') . 'templates' . DIRECTORY_SEPARATOR . 'authoring.tpl');
        $form = new taoSimpleDelivery_actions_form_ContentForm($this->getClass(), $content);
        $widget->setData('formContent', $form->getForm()
            ->render());
        $widget->setData('saveUrl', _url('save', 'Authoring', 'taoSimpleDelivery'));
        $widget->setData('formId', $form->getForm()->getName());
		return $widget->render();
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function createContent($tests = array()) {
        $content = $this->getClass()->createInstance();
        return $content;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onTestModelSet()
     */
    public function delete(core_kernel_classes_Resource $content) {
    	$content->delete();
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::cloneContent()
     */
    public function cloneContent(core_kernel_classes_Resource $content) {
        return $content->duplicate();
    }
    
    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::onChangeTestLabel()
     */
    public function onChangeDeliveryLabel(core_kernel_classes_Resource $delivery) {
        // nothing to do
    }

    public function getCompilerClass() {
        return 'taoSimpleDelivery_models_classes_DeliveryCompiler';
    }
    
}