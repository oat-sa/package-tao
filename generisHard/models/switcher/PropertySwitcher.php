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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */

namespace oat\generisHard\models\switcher;

use oat\generisHard\models\hardapi\ResourceReferencer;
use oat\generisHard\models\hardapi\Utils;

/**
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package generisHard
 
 */
class PropertySwitcher
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The Class that belongs the properties to switch
     *
     * @access protected
     * @var Class
     */
    protected $class = null;

    /**
     * Get the properties between the class and all it's parent until this
     *
     * @access protected
     * @var Class
     */
    protected $topclass = null;

    /**
     * Short description of attribute _properties
     *
     * @access private
     * @var array
     */
    private $_properties = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class class
     * @param  Class topclass Instanciate the property swicther with 
the class that belongs the properties to switch.
The topclass enables you to define an interval
bewteen a class and it's parent to retrieve the properties.
     * @return mixed
     */
    public function __construct( \core_kernel_classes_Class $class,  \core_kernel_classes_Class $topclass = null)
    {
        
        
    	$this->class = $class;
        $this->topclass = $topclass;
    	
        
    }

    /**
     * Found all the properties of the class. 
     * It gets also the parent's properties between
     * the class and the topclass. 
     * If the topclass is not defined, the GenerisResource class is used.
     * If there is more than one parent's class, the best path is calculated.
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    protected function findProperties()
    {
        $returnValue = array();

        
        
        if(is_null($this->topclass)){
			$parents = $this->class->getParentClasses(true);
		}
		else{
			
			//determine the parent path
			$parents = array();
			$top = false;
			do{
				if(!isset($lastLevelParents)){
					$parentClasses = $this->class->getParentClasses(false);
				}
				else{
					$parentClasses = array();
					foreach($lastLevelParents as $parent){
						$parentClasses = array_merge($parentClasses, $parent->getParentClasses(false));
					}
				}
				if(count($parentClasses) == 0){
					break;
				}
				$lastLevelParents = array();
				foreach($parentClasses as $parentClass){
					if($parentClass->getUri() == RDFS_CLASS){
						continue;
					}
					if($parentClass->equals($this->topclass)) {
						$parents[$parentClass->getUri()] = $parentClass;	
						$top = true;
						break;
					}
					
					$allParentClasses = $parentClass->getParentClasses(true);
					if(array_key_exists($this->topclass->getUri(), $allParentClasses)){
						 $parents[$parentClass->getUri()] = $parentClass;
					}
					$lastLevelParents[$parentClass->getUri()] = $parentClass;
				}
			}while(!$top);
		}
		$returnValue = array_merge(
			array(
				RDFS_LABEL 		=> new \core_kernel_classes_Property(RDFS_LABEL),
				RDFS_COMMENT	=> new \core_kernel_classes_Property(RDFS_COMMENT)
			),
			$this->class->getProperties(false)
		);
		foreach($parents as $parent){
			$returnValue = array_merge($returnValue, $parent->getProperties(false));
    	}
    	$this->_properties = $returnValue;
        
        

        return (array) $returnValue;
    }

    /**
     * The only way to retrieve the found properties.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param array additionalProp
     * @return array
     */
    public function getProperties($additionalProps=array())
    {
        $returnValue = array();

        
        
        if(count($this->_properties) == 0){
    		$returnValue = $this->findProperties();
    	}
    	else{
    		$returnValue = $this->_properties;
    	}
    	
        foreach ($additionalProps as $additionalProp){
        	$returnValue[$additionalProp->getUri()] = $additionalProp;
        }
    	
        

        return (array) $returnValue;
    }

    /**
     * Analyse the properties to find the best way to 
     * switch the create columns from the properties.
     * The columns is an associative array with a particular format:
     *  name  is the column name
     *  foreign is the name of the foreign table is reference
     *  multiple if it must be managed in a separate table
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param array additionalProp
     * @return array
     */
    public function getTableColumns($additionalProps = array(), $blackListedProps = array())
    {
        $returnValue = array();

        
        
        $properties = $this->getProperties($additionalProps);
                
    	/// HERE repalce what the switcher is doing: determine the column type: literal/class, translate, multiple values
    	foreach($properties as $property){

                $column = array('name' => Utils::getShortName($property));

                $range = $property->getRange();

                if(!is_null($range) && $range->getUri() != RDFS_LITERAL && !in_array($range->getUri(), $blackListedProps)){
                        //constraint to the class that represents the range

                        $column['foreign'] = '_'.Utils::getShortName($range);
                }

                if ($property->isLgDependent() === true || $property->isMultiple()=== true ){
                        //to put to the side table
                        $column['multi'] = true;
                }
                $returnValue[] = $column;
        }
		
		
        

        return (array) $returnValue;
    }

    public function propertyDescriptor(\core_kernel_classes_Property $property, $hardRangeClassOnly = false){

    	$returnValue = array(
		   'name'   => Utils::getShortName($property),
		   'isMultiple'  => $property->isMultiple(),
		   'isLgDependent' => $property->isLgDependent(),
		   'range'   => array()
    	);

    	$range = $property->getRange();
    	$rangeClassName = Utils::getShortName($range);
    	if($hardRangeClassOnly){
    		if(ResourceReferencer::singleton()->isClassReferenced($range)){
    			$returnValue[] = $rangeClassName;
    		}
    	}else{
    		$returnValue[] = $rangeClassName;
    	}

    	return (array) $returnValue;

    }
    
}