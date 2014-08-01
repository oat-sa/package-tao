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
 * A Delivery compiler
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 
 */
abstract class taoDelivery_models_classes_DeliveryCompiler extends tao_models_classes_Compiler
{
    /**
     * @param core_kernel_classes_Resource $resource
     * @throws common_exception_Error
     * @return taoDelivery_models_classes_DeliveryCompiler
     */
    public static function createCompiler($resource) {
        $storage = new taoDelivery_models_classes_TrackedStorage();
        $templateService = taoDelivery_models_classes_DeliveryTemplateService::singleton();
        $compilerClass = $templateService->getImplementationByContent($resource)->getCompilerClass();
        
        if (!class_exists($compilerClass)) {
            throw new common_exception_Error('Class '.$compilerClass.' not found while instanciating Compiler');
        }
        if (!is_subclass_of($compilerClass, __CLASS__)) {
            throw new common_exception_Error('Compiler class '.$compilerClass.' is not a compiler');
        }
        return new $compilerClass($resource, $storage);
    }

    protected function getSubCompilerClass(core_kernel_classes_Resource $resource) {
        return taoTests_models_classes_TestsService::singleton()->getCompilerClass($resource);
    }
    
    public function getSpawnedDirectoryIds() {
        return $this->getStorage()->getSpawnedDirectoryIds();
    }
}