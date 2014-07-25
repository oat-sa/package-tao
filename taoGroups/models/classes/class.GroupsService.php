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
 * Service methods to manage the Groups business models using the RDF API.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoGroups
 * @subpackage models_classes
 */
class taoGroups_models_classes_GroupsService
    extends tao_models_classes_ClassService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The RDFS top level group class
     *
     * @access protected
     * @var Class
     */
    protected $groupClass = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1-506607cb:1249f78eef0:-8000:0000000000001AEB begin
		
		parent::__construct();
		$this->groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
		
        // section 127-0-1-1-506607cb:1249f78eef0:-8000:0000000000001AEB end
    }

    /**
     * return the group top level class
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Class
     */
    public function getRootClass()
    {
        return $this->groupClass;
    }

    /**
     * subclass the Group class or one of it's subclass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @param  array properties
     * @return core_kernel_classes_Class
     */
    public function createGroupClass( core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    {
        $returnValue = null;

        // section 127-0-1-1-5109b15:124a4877945:-8000:0000000000001B11 begin
		
		if(is_null($clazz)){
			$clazz = $this->groupClass;
		}
		
		if($this->isGroupClass($clazz)){
		
			$groupClass = $this->createSubClass($clazz, $label);
			
			foreach($properties as $propertyName => $propertyValue){
				$myProperty = $groupClass->createProperty(
					$propertyName,
					$propertyName . ' ' . $label .' property created from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
				);
				
				//@todo implement check if there is a widget key and/or a range key
			}
			$returnValue = $groupClass;
		}
		
        // section 127-0-1-1-5109b15:124a4877945:-8000:0000000000001B11 end

        return $returnValue;
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

        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001806 begin
		
		if(!is_null($group)){
			$returnValue = $group->delete();
		}
		
        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001806 end

        return (bool) $returnValue;
    }

    /**
     * delete a group class or sublcass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function deleteGroupClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-5109b15:124a4877945:-8000:0000000000001B0D begin
		
		if(!is_null($clazz)){
			if($this->isGroupClass($clazz) && !$clazz->equals($this->groupClass)){
				$returnValue = $clazz->delete();
			}
		}
		
        // section 127-0-1-1-5109b15:124a4877945:-8000:0000000000001B0D end

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
        $returnValue = (bool) false;

        // section 127-0-1-1--5cd530d7:1249feedb80:-8000:0000000000001AEA begin
		
		if($clazz->equals($this->groupClass)) {
			$returnValue = true;	
		}
		else{
			foreach($this->groupClass->getSubClasses(true) as $subclass){
				if($clazz->equals($subclass)){
					$returnValue = true;
					break;	
				}
			}
		}
		
        // section 127-0-1-1--5cd530d7:1249feedb80:-8000:0000000000001AEA end

        return (bool) $returnValue;
    }

    /**
     * get the groups of a user
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string userUri
     * @return array resources of group
     */
    public function getGroups($userUri)
    {
        $groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
        return $groupClass->searchInstances(array(TAO_GROUP_MEMBERS_PROP => $userUri), array('like'=>false, 'recursive' => true));
    }
    
    /**
     * get the list of subjects linked to the group in parameter
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource group
     * @return array
     */
    public function getRelatedSubjects( core_kernel_classes_Resource $group)
    {
        $returnValue = array();

        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D44 begin
		
		if(!is_null($group)){
			$subjects = $group->getPropertyValues(new core_kernel_classes_Property(TAO_GROUP_MEMBERS_PROP));
			
			if(count($subjects) > 0){
				$subjectClass = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);
				$subjectSubClasses = array();
				foreach($subjectClass->getSubClasses(true) as $subjectSubClass){
					$subjectSubClasses[] = $subjectSubClass->getUri();
				}
				foreach($subjects as $subjectUri){
					if(!empty($subjectUri)){
						$clazz = $this->getClass(new core_kernel_classes_Resource($subjectUri));
						if(!is_null($clazz)){
							if(in_array($clazz->getUri(), $subjectSubClasses)){
								$returnValue[] = $clazz->getUri();
							}
						}
						$returnValue[] = $subjectUri;
					}
				}
			}
		}
		
        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D44 end

        return (array) $returnValue;
    }

    /**
     * define the list of subjects composing a group
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource group
     * @param  array subjects
     * @return boolean
     */
    public function setRelatedSubjects( core_kernel_classes_Resource $group, $subjects = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D48 begin
		
		if(!is_null($group)){
			
			$memberProp = new core_kernel_classes_Property(TAO_GROUP_MEMBERS_PROP);
			
			$group->removePropertyValues($memberProp);
			$done = 0;
			foreach($subjects as $subject){
				if($group->setPropertyValue($memberProp, $subject)){
					$done++;
				}
			}
			if($done == count($subjects)){
				$returnValue = true;
			}
		}
		
        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D48 end

        return (bool) $returnValue;
    }

    /**
     * get the list of delivery urls linked to the group in parameter
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource group
     * @return array
     * @deprecated
     */
    public function getRelatedDeliveries( core_kernel_classes_Resource $group)
    {
        $returnValue = array();
        foreach ($group->getPropertyValues(new core_kernel_classes_Property(TAO_GROUP_DELIVERIES_PROP)) as $delivery) {
            $returnValue[] = $delivery->getUri();
        }
        return $returnValue;
    }

    /**
     * define a list of deliveries linked to the group in parameter
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource group
     * @param  array deliveries
     * @return boolean
     */
    public function setRelatedDeliveries( core_kernel_classes_Resource $group, $deliveries = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-72374553:127198ee25a:-8000:0000000000001ED7 begin
		
		if(!is_null($group)){
			
			$deliveriesProp = new core_kernel_classes_Property(TAO_GROUP_DELIVERIES_PROP);
			
			$group->removePropertyValues($deliveriesProp);
			$done = 0;
			foreach($deliveries as $delivery){
				if($group->setPropertyValue($deliveriesProp, $delivery)){
					$done++;
				}
			}
			if($done == count($deliveries)){
				$returnValue = true;
			}
		}
		
        // section 127-0-1-1-72374553:127198ee25a:-8000:0000000000001ED7 end

        return (bool) $returnValue;
    }

} /* end of class taoGroups_models_classes_GroupsService */

?>