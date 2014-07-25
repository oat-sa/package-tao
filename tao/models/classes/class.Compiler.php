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
 * @subpackage models_classes
 */
abstract class tao_models_classes_Compiler
{
    private $resoure;
    
    public function __construct(core_kernel_classes_Resource $resource) {
        $this->resoure = $resource;
    }
    
    /**
     * @return core_kernel_classes_Resource
     */
    protected function getResource() {
        return $this->resoure;
    }
    
    /**
     * Creates an appropriate sub-directory for a resource's compilation
     * 
     * @param core_kernel_file_File $rootDirectory The root directory for this item compilation.
     * @param core_kernel_classes_Resource $resource The Item resource in the database.
     * @throws taoItems_models_classes_CompilationFailedException If something goes wrong while creating the sub-directory.
     * @return core_kernel_versioning_File The sub-directory.
     */
    protected function createSubDirectory(core_kernel_file_File $rootDirectory, core_kernel_classes_Resource $resource)
    {
        $resourceUri = $resource->getUri();
        return $this->createNamedSubDirectory($rootDirectory, $resource, substr($resourceUri, strpos($resourceUri, '#') + 1));
    }
    
    /**
     * Create an appropriate sub-directory for a resource's compilation with a specific $name.
     * 
     * @param core_kernel_file_File $rootDirectory The root directory of this item compilation.
     * @param core_kernel_classes_Resource $resource
     * @param string $name The name of the sub-directory to be created.
     * @throws taoItems_models_classes_CompilationFailedException If something goes wrong while creating the named sub-directory.
     */
    protected function createNamedSubDirectory(core_kernel_file_File $rootDirectory, core_kernel_classes_Resource $resource, $name) {
        $itemUri = $resource->getUri();
        $relPath = $rootDirectory->getRelativePath() . DIRECTORY_SEPARATOR . $name;
        $absPath = $rootDirectory->getAbsolutePath() . DIRECTORY_SEPARATOR . $name;
        
        if (!is_dir($absPath) && !mkdir($absPath)) {
            throw new taoItems_models_classes_CompilationFailedException("Could not create sub-directory '${absPath}' while compiling item '${itemUri}'.");
        }
        
        return $rootDirectory->getFileSystem()->createFile('', $relPath);
    }
    
    /**
     * Compile the resource into a runnable service
     * using the provided directory as storage
     * 
     * @param core_kernel_file_File $destinationDirectory
     * @return tao_models_classes_service_ServiceCall
     * @throws tao_models_classes_CompilationFailedException
     */
    public abstract function compile(core_kernel_file_File $destinationDirectory);
}