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
 * An abstract compiler
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 
 */
abstract class tao_models_classes_Compiler
{
    /**
     * Resource to be compiled
     * @var core_kernel_classes_Resource
     */
    private $resoure;
    
    /**
     * @var tao_models_classes_service_FileStorage
     */
    private $compilationStorage = null;
    
    /**
     * 
     * @param core_kernel_classes_Resource $resource
     */
    public function __construct(core_kernel_classes_Resource $resource, tao_models_classes_service_FileStorage $storage) {
        $this->resoure = $resource;
        $this->compilationStorage = $storage;
    }
    
    /**
     * Returns the storage to be used during compilation
     * 
     * @return tao_models_classes_service_FileStorage
     */
    protected function getStorage() {
        return $this->compilationStorage;
    }
    
    /**
     * @return core_kernel_classes_Resource
     */
    protected function getResource() {
        return $this->resoure;
    }
    
    /**
     * Returns a directory that is accessible to the client
     * 
     * @return tao_models_classes_service_StorageDirectory
     */
    protected function spawnPublicDirectory() {
        return $this->compilationStorage->spawnDirectory(true);
    }
    
    /**
     * Returns a directory that is not accessible to the client
     * 
     * @return tao_models_classes_service_StorageDirectory
     */
    protected function spawnPrivateDirectory() {
        return $this->compilationStorage->spawnDirectory(false);
    }

    /**
     * helper to create a fail report
     * 
     * @param string $userMessage
     * @return common_report_Report
     */
    protected function fail($userMessage) {
        return new common_report_Report(
            common_report_Report::TYPE_ERROR,
            $userMessage
        );
    }
    
    /**
     * Determin the compiler of the resource
     * 
     * 
     * @param core_kernel_classes_Resource $resource
     * @return string the name of the compiler class
     */
    protected abstract function getSubCompilerClass(core_kernel_classes_Resource $resource);
    
    /**
     * Compile a subelement of the current resource
     * 
     * @param core_kernel_classes_Resource $resource
     * @return common_report_Report returns a report that if successful contains the service call
     */
    protected function subCompile(core_kernel_classes_Resource $resource) {
        $compilerClass = $this->getSubCompilerClass($resource);
        if (!class_exists($compilerClass)) {
            common_Logger::e('Class '.$compilerClass.' not found while instanciating Compiler');
            return $this->fail(__('%s is of a type that cannot be published', $resource->getLabel()));
        }
        if (!is_subclass_of($compilerClass, __CLASS__)) {
            common_Logger::e('Compiler class '.$compilerClass.' is not a compiler');
            return $this->fail(__('%s is of a type that cannot be published', $resource->getLabel()));
        }
        $compiler = new $compilerClass($resource, $this->getStorage());
        $report = $compiler->compile();
        return $report;
    }
    
    /**
     * Compile the resource into a runnable service
     * and returns a report that if successful contains the service call
     * 
     * @return common_report_Report
     * @throws tao_models_classes_CompilationFailedException
     */
    public abstract function compile();
    
}