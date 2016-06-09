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
 * Copyright (c) 2013- (original work) Open Assessment Technologies SA;
 *
 */
use oat\oatbox\service\ServiceManager;
/**
 * This service represents the actions applicable from a root class
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 *         
 */
abstract class tao_models_classes_ClassService
    extends tao_models_classes_GenerisService
{

    /**
     * Returns the root class of this service
     *
     * @return core_kernel_classes_Class
     */
    abstract public function getRootClass();

    /**
     * Delete a resource
     *
     * @param core_kernel_classes_Resource $resource            
     * @return boolean
     */
    public function deleteResource(core_kernel_classes_Resource $resource)
	{
	    return $resource->delete();
	}

    /**
     * Delete a subclass
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param core_kernel_classes_Class $clazz            
     * @return boolean
     */
    public function deleteClass(core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;
        
        if ($clazz->isSubClassOf($this->getRootClass()) && ! $clazz->equals($this->getRootClass())) {
            
            $returnValue = true;
            
            $subclasses = $clazz->getSubClasses(false);
            foreach ($subclasses as $subclass) {
                $returnValue = $returnValue && $this->deleteClass($subclass);
            }
            foreach ($clazz->getProperties() as $classProperty) {
                $returnValue = $returnValue && $this->deleteClassProperty($classProperty);
            }
            $returnValue = $returnValue && $clazz->delete();
            
        } else {
            common_Logger::w('Tried to delete class ' . $clazz->getUri() . ' as if it were a subclass of ' . $this->getRootClass()->getUri());
        }
        
        return (bool) $returnValue;
    }


    /**
     * remove a class property
     * 
     * @param core_kernel_classes_Property $property            
     * @return bool
     */
    public function deleteClassProperty(core_kernel_classes_Property $property){
        $indexes = $property->getPropertyValues(new core_kernel_classes_Property(INDEX_PROPERTY));

        //delete property and the existing values of this property
        if($returnValue = $property->delete(true)){
            //delete index linked to the property
            foreach($indexes as $indexUri){
                $index = new core_kernel_classes_Resource($indexUri);
                $returnValue = $this->deletePropertyIndex($index);
            }
        }

        return $returnValue;
    }

    /**
     * * remove an index property
     * @param core_kernel_classes_Resource $index
     * @return bool
     */
    public function deletePropertyIndex(core_kernel_classes_Resource $index){
        return $index->delete(true);
    }
    
    public function getServiceManager()
    {
        return ServiceManager::getServiceManager();
    }
}