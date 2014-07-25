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
 * 
 */

/**
 * 
 */
abstract class tao_models_classes_CrudService
    extends tao_models_classes_GenerisService
{
	public function isInScope($uri){
	    if (!(common_Utils::isUri($uri))){
		throw new common_exception_InvalidArgumentType();
	    }
	    $resource = new core_kernel_classes_Resource($uri);
	    return $resource->hasType($this->getRootClass());
	}

	/**
	 * @param string uri
	 * @throws common_Exception_NoContent, common_exception_InvalidArgumentType
	 * @return object
	 */
	public function get($uri){
		if (!common_Utils::isUri($uri)){
		    throw new common_exception_InvalidArgumentType();
		}
		if (!($this->isInScope($uri))){
		throw new common_exception_PreConditionFailure("The URI must be a valid resource under the root Class");
		}
		$resource = new core_kernel_classes_Resource($uri);
		return $resource->getResourceDescription(false);
	}
	public function getAll(){
		$resources = array();
		    foreach ($this->getRootClass()->getInstances(true) as $resource) {
			$resources[] = $resource->getResourceDescription(false);
		    }
		return $resources;
	}
	public function delete($uri){
		if (!common_Utils::isUri($uri)){
		    throw new common_exception_InvalidArgumentType();
		}
		if (!($this->isInScope($uri))){
		throw new common_exception_PreConditionFailure("The URI must be a valid resource under the root Class");
		}
		$resource = new core_kernel_classes_Resource($uri);
		//if the resource does not exist, indicate a not found exception
		if (count(
		    $resource->getRdfTriples()->sequence
			    ) == 0){
			throw new common_exception_NoContent();
		    }
		    $resource->delete();

	}
	public function deleteAll(){
	    $resources = array();
	    foreach ($this->getRootClass()->getInstances(true) as $resource) {
		$resource->delete();
	    }
	}
	public function create($label = "", $type = null, $propertiesValues= array()){
		$type = (isset($type)) ? new core_kernel_classes_Class($type) : $this->getRootClass();
		$resource = parent::createInstance( $type, $label);
		$resource->setPropertiesValues($propertiesValues);
		return $resource;
	}

	public function update($uri , $propertiesValues = array()){
		if (!common_Utils::isUri($uri)){
		    throw new common_exception_InvalidArgumentType();
		}
		if (!($this->isInScope($uri))){
		throw new common_exception_PreConditionFailure("The URI must be a valid resource under the root Class");
		}
		$resource = new core_kernel_classes_Resource($uri);
		//if the resource does not exist, indicate a not found exception
		    if (count(
			    $resource->getRdfTriples()->sequence
			    ) == 0){
			throw new common_exception_NoContent();
		    }
		foreach ($propertiesValues as $uri =>$parameterValue){
		    $resource->editPropertyValues(new core_kernel_classes_Property($uri), $parameterValue);
		}
		return $resource;
	}
} /* end of abstract class tao_models_classes_GenerisService */

?>