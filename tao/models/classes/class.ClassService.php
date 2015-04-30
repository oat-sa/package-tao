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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * This service represents the actions applicable from a root class
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
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
	 * @param  Class clazz
	 * @return boolean
	 */
	public function deleteClass(core_kernel_classes_Class $clazz)
	{
	    $returnValue = (bool) false;
	
        if($clazz->isSubClassOf($this->getRootClass()) && !$clazz->equals($this->getRootClass())) {
            $returnValue = $clazz->delete();
        } else {
            common_Logger::w('Tried to delete class '.$clazz->getUri().' as if it were a subclass of '.$this->getRootClass()->getUri());
        }
	
	    return (bool) $returnValue;
	}
}