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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-2014 (update and modification) Open Assessment Technologies SA
 */

namespace oat\taoGroups\models;

use \core_kernel_classes_Class;
use \core_kernel_classes_Property;
use \core_kernel_classes_Resource;
use \tao_models_classes_ClassService;
use oat\taoTestTaker\models\TestTakerService;
use oat\oatbox\user\User;

/**
 * Service methods to manage the Groups business models using the RDF API.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoGroups
 
 */
class GroupsService
    extends tao_models_classes_ClassService
{
    const CLASS_URI = 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group';

    const PROPERTY_MEMBERS_URI = 'http://www.tao.lu/Ontologies/TAOGroup.rdf#member';
    
    /**
     * return the group top level class
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Class
     */
    public function getRootClass()
    {
        return new core_kernel_classes_Class(self::CLASS_URI);
    }

    /**
     * delete a group instance
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource group
     * @return boolean
     */
    public function deleteGroup( core_kernel_classes_Resource $group)
    {
        $returnValue = (bool) false;
	
		if(!is_null($group)){
			$returnValue = $group->delete();
		}
	    return (bool) $returnValue;
    }

    /**
     * Check if the Class in parameter is a subclass of the Group Class
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function isGroupClass( core_kernel_classes_Class $clazz)
    {
        return $clazz->equals($this->getRootClass()) || $clazz->isSubClassOf($this->getRootClass());
    }

    /**
     * get the groups of a user
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string userUri
     * @return array resources of group
     */
    public function getGroups(User $user)
    {
        $groups = $user->getPropertyValues(self::PROPERTY_MEMBERS_URI);
        array_walk($groups, function(&$group) { $group = new core_kernel_classes_Resource($group);});
        return $groups;
    }
    
    /**
     * gets the users of a group
     * 
     * @param string $groupUri
     * @return array resources of users
     */
    public function getUsers($groupUri)
    {
        $subjectClass = TestTakerService::singleton()->getRootClass();
        $users = $subjectClass->searchInstances(array(
        	self::PROPERTY_MEMBERS_URI => $groupUri
        ), array(
        	'recursive' => true, 'like' => false
        ));
        return $users;
    }
    
    public function addUser($userUri, core_kernel_classes_Resource $group) {
        $user = new \core_kernel_classes_Resource($userUri);
        return $user->setPropertyValue(new core_kernel_classes_Property(self::PROPERTY_MEMBERS_URI), $group);
    }
    
    public function removeUser(\core_kernel_classes_Resource $user, core_kernel_classes_Resource $group) {
        return $user->removePropertyValue(new core_kernel_classes_Property(self::PROPERTY_MEMBERS_URI), $group);
    }
}
