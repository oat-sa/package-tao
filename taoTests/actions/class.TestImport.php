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
 * This controller provide the actions to import items 
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 
 *
 */
class taoTests_actions_TestImport extends tao_actions_Import {
	
    /**
     * overwrite the parent index to add the requiresRight for Tests
     *
     * @requiresRight id WRITE
     * @see tao_actions_Import::index()
     */
    public function index()
    {
        parent::index();
    }
    
	protected function getAvailableImportHandlers() {
		$returnValue = parent::getAvailableImportHandlers();

		$testModelClass = new core_kernel_classes_Class(CLASS_TESTMODEL); 
		foreach ($testModelClass->getInstances() as $model) {
			$impl = taoTests_models_classes_TestsService::singleton()->getTestModelImplementation($model);
			if (in_array('tao_models_classes_import_ImportProvider', class_implements($impl))) {
				foreach ($impl->getImportHandlers() as $handler) {
					array_unshift($returnValue, $handler);
				}
			}
		}
		
		return $returnValue;
	}
}