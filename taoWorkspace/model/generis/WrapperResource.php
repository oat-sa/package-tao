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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 *
 */
namespace oat\taoWorkspace\model\generis;

use core_kernel_persistence_ResourceInterface;
use core_kernel_classes_Resource;
use core_kernel_classes_Property;
use core_kernel_classes_Class;
use oat\taoWorkspace\model\WorkspaceMap;

/**
 * Short description of class core_kernel_persistence_smoothsql_Resource
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 
 */
class WrapperResource
    implements core_kernel_persistence_ResourceInterface
{

    /**
     * @var core_kernel_persistence_ResourceInterface
     */
    private $inner;
    
    private $workspace;
    
    private $mapper;
    
    public function __construct(core_kernel_persistence_ResourceInterface $inner, core_kernel_persistence_ResourceInterface $workspace) {
        $this->inner = $inner;
        $this->workspace = $workspace;
        $this->mapper = WorkspaceMap::getCurrentUserMap();
    }
    
    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::getTypes()
     */
    public function getTypes(core_kernel_classes_Resource $resource)
    {
        return $this->getModelForResource($resource)->getTypes($this->mapResource($resource));
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::getPropertyValues()
     */
    public function getPropertyValues(core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $options = array())
    {
        return $this->getModelForResource($resource)->getPropertyValues($this->mapResource($resource), $property, $options);
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::getPropertyValuesByLg()
     */
    public function getPropertyValuesByLg(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $lg)
    {
        return $this->getModelForResource($resource)->getPropertyValuesByLg($this->mapResource($resource), $property, $lg);
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::setPropertyValue()
     */
    public function setPropertyValue(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $object, $lg = null)
    {
        return $this->getModelForResource($resource)->setPropertyValue($this->mapResource($resource), $property, $object, $lg);
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::setPropertiesValues()
     */
    public function setPropertiesValues(core_kernel_classes_Resource $resource, $properties)
    {
        return $this->getModelForResource($resource)->setPropertiesValues($this->mapResource($resource), $properties);
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::setPropertyValueByLg()
     */
    public function setPropertyValueByLg(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $value, $lg)
    {
        return $this->getModelForResource($resource)->setPropertyValueByLg($this->mapResource($resource), $property, $value, $lg);
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::removePropertyValues()
     */
    public function removePropertyValues(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $options = array())
    {
        return $this->getModelForResource($resource)->removePropertyValues($this->mapResource($resource), $property, $options);
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::removePropertyValueByLg()
     */
    public function removePropertyValueByLg(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $lg, $options = array())
    {
        return $this->getModelForResource($resource)->removePropertyValueByLg($this->mapResource($resource), $property, $lg, $options);
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::getRdfTriples()
     */
    public function getRdfTriples(core_kernel_classes_Resource $resource)
    {
        return $this->getModelForResource($resource)->getRdfTriples($this->mapResource($resource));
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::getUsedLanguages()
     */
    public function getUsedLanguages(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property)
    {
        return $this->getModelForResource($resource)->getUsedLanguages($this->mapResource($resource), $property);
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::duplicate()
     */
    public function duplicate(core_kernel_classes_Resource $resource, $excludedProperties = array())
    {
        return $this->getModelForResource($resource)->duplicate($this->mapResource($resource), $excludedProperties);
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::delete()
     */
    public function delete(core_kernel_classes_Resource $resource, $deleteReference = false)
    {
        return $this->getModelForResource($resource)->delete($this->mapResource($resource), $deleteReference);
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::getPropertiesValues()
     */
    public function getPropertiesValues(core_kernel_classes_Resource $resource, $properties)
    {
        return $this->getModelForResource($resource)->getPropertiesValues($this->mapResource($resource), $properties);
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::setType()
     */
    public function setType(core_kernel_classes_Resource $resource, core_kernel_classes_Class $class)
    {
        return $this->getModelForResource($resource)->setType($this->mapResource($resource), $class);
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ResourceInterface::removeType()
     */
    public function removeType(core_kernel_classes_Resource $resource, core_kernel_classes_Class $class)
    {
        return $this->getModelForResource($resource)->removeType($this->mapResource($resource), $class);
    }
    
    private function getModelForResource(core_kernel_classes_Resource $resource)
    {
        if ($this->mapResource($resource)->equals($resource)) {
            return $this->inner;
        } else {
            return $this->workspace;
        }
    }
    
    /**
     * Map the called resource to the actual resource
     * 
     * @param core_kernel_classes_Resource $resource
     * @return core_kernel_classes_Resource
     */
    private function mapResource(core_kernel_classes_Resource $resource)
    {
        return $this->mapper->map($resource);
    }
}
