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

namespace oat\generisHard\models\hardsql;

use oat\generisHard\models\hardapi\Utils as HardapiUtils;
use oat\generisHard\models\hardapi\Exception as HardapiException;
use oat\generisHard\models\hardapi\ResourceReferencer;

/**
 * Short description of class self
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generisHard
 
 */
class Property
    extends \core_kernel_persistence_PersistenceImpl
        implements \core_kernel_persistence_PropertyInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var Resource
     */
    public static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getSubProperties
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getSubProperties( \core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        
        throw new \core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        

        return (array) $returnValue;
    }

    /**
     * Short description of method isLgDependent
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isLgDependent( \core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        
        throw new \core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method isMultiple
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isMultiple( \core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        
        throw new \core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRange
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return \core_kernel_classes_Class
     */
    public function getRange( \core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        
        throw new \core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        

        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete( \core_kernel_classes_Resource $resource, $deleteReference = false)
    {
        $returnValue = (bool) false;

        
        
        throw new \core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method setRange
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Class class
     * @return \core_kernel_classes_Class
     */
    public function setRange( \core_kernel_classes_Resource $resource,  \core_kernel_classes_Class $class)
    {
        $returnValue = null;

        
        
        // always remain in smooth mode.
        $returnValue = \core_kernel_persistence_smoothsql_Property::singleton()->setRange($resource, $class);

        

        return $returnValue;
    }

    /**
     * Short description of method setMultiple
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean isMultiple
     * @return void
     */
    public function setMultiple( \core_kernel_classes_Resource $resource, $isMultiple)
    {
        
        
        // First, do the same as in smooth mode.
        \core_kernel_persistence_smoothsql_Property::singleton()->setMultiple($resource, $isMultiple);
        
    	// Second, we alter the relevant table(s) if needed.
        // For all the classes that have the resource as domain,
        // we have to alter the correspondent tables.
        $referencer = ResourceReferencer::singleton();
        $dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        $propertyDescription = HardapiUtils::propertyDescriptor($resource);
        
        $wasMulti = $propertyDescription['isMultiple'];
        $wasLgDependent = $propertyDescription['isLgDependent'];
        
        // @TODO $batchSize's value is arbitrary.
        $batchSize = 100;	// Transfer data $batchSize by $batchSize.
        
        if ($wasMulti != $isMultiple){
        	
        	try{
        		// The multiplicity is then changing.
        		// However, if the property was not 'multiple' but 'language dependent'
        		// it is already stored as it should.
        		if ($isMultiple == true && $wasLgDependent == false && $wasMulti == false){
        			
        			// We go from single to multiple.
        			HardapiUtils::scalarToMultiple($resource, $batchSize);
        		}
        		else if ($isMultiple == false && ($wasLgDependent == true || $wasMulti == true)){
        			
        			// We go from multiple to single.
        			HardapiUtils::multipleToScalar($resource, $batchSize);
        		}
        	}
        	catch (HardapiException $e){
        		throw new Exception($e->getMessage());
        	}
        }
        
        
    }

    /**
     * Short description of method setLgDependent
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean isLgDependent
     * @return void
     */
    public function setLgDependent( \core_kernel_classes_Resource $resource, $isLgDependent)
    {
        
        // First, do the same as in smooth mode.
        \core_kernel_persistence_smoothsql_Property::singleton()->setLgDependent($resource, $isLgDependent);
        
    	// Second, we alter the relevant table(s) if needed.
        // For all the classes that have the resource as domain,
        // we have to alter the correspondent tables.
        $referencer = ResourceReferencer::singleton();
        $dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        $propertyDescription = HardapiUtils::propertyDescriptor($resource);
        
        $wasMulti = $propertyDescription['isMultiple'];
        $wasLgDependent = $propertyDescription['isLgDependent'];
        
        // @TODO $batchSize's value is arbitrary.
        $batchSize = 100;	// Transfer data $batchSize by $batchSize.
        
        if ($wasLgDependent != $isLgDependent){
        	
        	try{
        		// The multiplicity is then changing.
        		// However, if the property was not 'language dependent' but 'multiple'
        		// it is already stored as it should.
        		if ($isLgDependent == true && $wasMulti == false && $wasLgDependent == false){
        			
        			// We go from single to multiple.
        			HardapiUtils::scalarToMultiple($resource, $batchSize);
        		}
        		else if ($isLgDependent == false && ($wasMulti == true || $wasLgDependent == true)){
        			
        			// We go from multiple to single.
        			HardapiUtils::multipleToScalar($resource, $batchSize);
        		}
        	}
        	catch (HardapiException $e){
        		throw new Exception($e->getMessage());
        	}
        }
        
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return \core_kernel_classes_Resource
     */
    public static function singleton()
    {
        $returnValue = null;

        
        
        if (self::$instance == null){
        	self::$instance = new self();
        }
        $returnValue = self::$instance;
        
        

        return $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isValidContext( \core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        
        $returnValue = ResourceReferencer::singleton()->isPropertyReferenced($resource);
        

        return (bool) $returnValue;
    }

}