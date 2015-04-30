<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA
 * 
 */


/**
 *
 * Crud services implements basic CRUD services, orginally intended for
 * REST controllers/ HTTP exception handlers.
 * Consequently the signatures and behaviors is closer to REST and throwing
 * HTTP like exceptions.
 *
 * @author Patrick Plichart, patrick@taotesting.com
 *        
 */
abstract class tao_models_classes_CrudService extends tao_models_classes_Service
{

    /**
     *
     * @author Patrick Plichart, patrick@taotesting.com
     * @param string $uri            
     * @throws common_exception_InvalidArgumentType
     * @return boolean
     */
    public function isInScope($uri)
    {
        if (!(common_Utils::isUri($uri))) {
            throw new common_exception_InvalidArgumentType();
        }
        $resource = new core_kernel_classes_Resource($uri);
        return $resource->hasType($this->getRootClass());
    }

    /**
     *
     * @author Patrick Plichart, patrick@taotesting.com
     * return tao_models_classes_ClassService
     */
    protected abstract function getClassService();

    /**
     *
     * @author Patrick Plichart, patrick@taotesting.com
     * return core_kernel_classes_Class
     */
    protected function getRootClass()
    {
        return $this->getClassService()->getRootClass();
    }

    /**
     *
     * @param string uri
     * @throws common_Exception_NoContent
     * @throws common_exception_InvalidArgumentType
     * @return object
     */
    public function get($uri)
    {
        if (!common_Utils::isUri($uri)) {
            throw new common_exception_InvalidArgumentType();
        }
        if (!($this->isInScope($uri))) {
            throw new common_exception_PreConditionFailure("The URI must be a valid resource under the root Class");
        }
        $resource = new core_kernel_classes_Resource($uri);
        $formater = new core_kernel_classes_ResourceFormatter();
        return $formater->getResourceDescription($resource,false);
    }

    /**
     *
     * @author Patrick Plichart, patrick@taotesting.com
     * @return stdClass
     */
    public function getAll()
    {
        $formater = new core_kernel_classes_ResourceFormatter();
        $resources = array();
        foreach ($this->getRootClass()->getInstances(true) as $resource) {
            $resources[] = $formater->getResourceDescription($resource,false);
        }
        return $resources;
    }

    /**
     *
     * @author Patrick Plichart, patrick@taotesting.com
     * @param string $uri            
     * @throws common_exception_InvalidArgumentType
     * @throws common_exception_PreConditionFailure
     * @throws common_exception_NoContent
     */
    public function delete($uri)
    {
        if (!common_Utils::isUri($uri)) {
            throw new common_exception_InvalidArgumentType();
        }
        if (!($this->isInScope($uri))) {
            throw new common_exception_PreConditionFailure("The URI must be a valid resource under the root Class");
        }
        $resource = new core_kernel_classes_Resource($uri);
        // if the resource does not exist, indicate a not found exception
        if (count($resource->getRdfTriples()->sequence) == 0) {
            throw new common_exception_NoContent();
        }
        $resource->delete();
    }

    /**
     *
     * @author Patrick Plichart, patrick@taotesting.com
     */
    public function deleteAll()
    {
        $resources = array();
        foreach ($this->getRootClass()->getInstances(true) as $resource) {
            $resource->delete();
        }
    }

    /**
     *
     * @author Patrick Plichart, patrick@taotesting.com
     * @param string $label            
     * @param string $type            
     * @param array $propertiesValues            
     * @return core_kernel_classes_Resource
     */
    public function create($label = "", $type = null, $propertiesValues = array())
    {
        $type = (isset($type)) ? new core_kernel_classes_Class($type) : $this->getRootClass();
        
        $resource = $this->getClassService()->createInstance($type, $label);
        $resource->setPropertiesValues($propertiesValues);
        return $resource;
    }

    /**
     *
     * @author Patrick Plichart, patrick@taotesting.com
     * @param string $uri            
     * @param array $propertiesValues            
     * @throws common_exception_InvalidArgumentType
     * @throws common_exception_PreConditionFailure
     * @throws common_exception_NoContent
     * @return core_kernel_classes_Resource
     */
    public function update($uri, $propertiesValues = array())
    {
        if(!common_Utils::isUri($uri)) {
            throw new common_exception_InvalidArgumentType();
        }
        if(!($this->isInScope($uri))) {
            throw new common_exception_PreConditionFailure("The URI must be a valid resource under the root Class");
        }
        $resource = new core_kernel_classes_Resource($uri);
        // if the resource does not exist, indicate a not found exception
        if(count($resource->getRdfTriples()->sequence) == 0) {
            throw new common_exception_NoContent();
        }
        foreach($propertiesValues as $uri => $parameterValue) {
            $resource->editPropertyValues(new core_kernel_classes_Property($uri), $parameterValue);
        }
        return $resource;
    }
}

?>