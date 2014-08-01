<?php
/*  
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * the wfEngine TestModel
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoWfTest
 
 */
class taoWfAdvTest_models_classes_WfAdvTestModel
	extends taoWfTest_models_classes_WfTestModel
	implements taoTests_models_classes_TestModel
{
    // --- ASSOCIATIONS ---

    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * (non-PHPdoc)
     * @see taoTests_models_classes_TestModel::getAuthoring()
     */
    public function getAuthoring( core_kernel_classes_Resource $test) {
    	$process = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
 		$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoWfAdvTest');
    	$widget = new Renderer($ext->getConstant('DIR_VIEWS').'templates'.DIRECTORY_SEPARATOR.'authoring.tpl');
		$widget->setData('processUri', $process->getUri());
		$widget->setData('label', __('Authoring %s', $test->getLabel()));
    	return $widget->render();
    }
}

?>