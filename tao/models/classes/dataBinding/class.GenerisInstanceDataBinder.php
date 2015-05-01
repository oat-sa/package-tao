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
 * 
 */

/**
 * A data binder focusing on binding a source of data to a generis instance
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
class tao_models_classes_dataBinding_GenerisInstanceDataBinder
    extends tao_models_classes_dataBinding_AbstractDataBinder
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * A target Resource.
     *
     * @access private
     * @var Resource
     */
    private $targetInstance = null;

    // --- OPERATIONS ---

    /**
     * Creates a new instance of binder.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource targetInstance The
     * @return mixed
     */
    public function __construct( core_kernel_classes_Resource $targetInstance)
    {
        
        $this->targetInstance = $targetInstance;
        
    }

    /**
     * Returns the target instance.
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return core_kernel_classes_Resource
     */
    protected function getTargetInstance()
    {
        $returnValue = null;

        
        $returnValue = $this->targetInstance;
        

        return $returnValue;
    }

    /**
     * Simply bind data from the source to a specific generis class instance.
     *
     * The array of the data to be bound must contain keys that are property
     * The repspective values can be either scalar or vector (array) values or
     * values.
     *
     * - If the element of the $data array is scalar, it is simply bound using
     * - If the element of the $data array is a vector, the property values are
     * with the values of the vector.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  array data An array of values where keys are Property URIs and values are either scalar or vector values.
     * @return mixed
     */
    public function bind($data)
    {
        $returnValue = null;

        
        
        // Some predicates must be excluded.
        // e.g. 'tao.forms.instance' which is only a tag to identify
        // forms dedicated to RDF Resources edition.
        $excludedPredicates = array('tao.forms.instance');
        
        try {
	        $instance = $this->getTargetInstance();
	        
	        foreach($data as $propertyUri => $propertyValue){
	        	
	        	if (false === in_array($propertyUri, $excludedPredicates)){
	        		if($propertyUri == RDF_TYPE){
	        			foreach($instance->getTypes() as $type){
	        				$instance->removeType($type);
	        			}
	        			if(!is_array($propertyValue)){
	        				$types = array($propertyValue) ;
	        			}
	        			foreach($types as $type){
	        				$instance->setType(new core_kernel_classes_Class($type));
	        			}
	        			continue;
	        		}
	        		 
	        		$prop = new core_kernel_classes_Property( $propertyUri );
	        		$values = $instance->getPropertyValuesCollection($prop);
	        		if($values->count() > 0){
	        			if(is_array($propertyValue)){
	        				$instance->removePropertyValues($prop);
	        				foreach($propertyValue as $aPropertyValue){
	        					$instance->setPropertyValue(
	        							$prop,
	        							$aPropertyValue
	        					);
	        				}
	        				 
	        			}
	        			else if (is_string($propertyValue)){
	        				$instance->editPropertyValues(
	        						$prop,
	        						$propertyValue
	        				);
	        				if(strlen(trim($propertyValue))==0){
	        					//if the property value is an empty space(the default value in a select input field), delete the corresponding triplet (and not all property values)
	        					$instance->removePropertyValues($prop, array('pattern' => ''));
	        				}
	        			}
	        		}
	        		else{
	        			 
	        			if(is_array($propertyValue)){
	        				 
	        				foreach($propertyValue as $aPropertyValue){
	        					$instance->setPropertyValue(
	        							$prop,
	        							$aPropertyValue
	        					);
	        				}
	        			}
	        			else if (is_string($propertyValue)){
	        				$instance->setPropertyValue(
	        						$prop,
	        						$propertyValue
	        				);
	        			}
	        		}
	        	}
	        }
	        
	        $returnValue = $instance;
        }
        catch (common_Exception $e){
        	$msg = "An error occured while binding property values to instance '': " . $e->getMessage();
        	$instanceUri = $instance->getUri();
        	throw new tao_models_classes_dataBinding_GenerisInstanceDataBindingException($msg);
        }
        

        return $returnValue;
    }

}

?>