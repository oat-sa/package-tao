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
 * Short description of class core_kernel_persistence_smoothsql_Property
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class core_kernel_persistence_smoothsql_Property
    extends core_kernel_persistence_smoothsql_Resource
        implements core_kernel_persistence_PropertyInterface
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
    public function getSubProperties( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        
        
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlQuery = "SELECT subject FROM statements WHERE predicate = '" . RDF_SUBPROPERTYOF . "' AND object = '".$resource->getUri()."'";
		$returnValue = array();
		$sqlResult = $dbWrapper->query($sqlQuery);
		while ($row = $sqlResult->fetch()){
			$property = new core_kernel_classes_Property($row['subject']);
			$returnValue[$property->getUri()] = $property;

			if($recursive == true) {
				$returnValue = array_merge($returnValue,$property->getSubProperties(true));
			}
		}
        
        

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
    public function isLgDependent( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        

        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        
        

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
    public function isMultiple( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        
        
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRange
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return core_kernel_classes_Class
     */
    public function getRange( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        

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
    public function delete( core_kernel_classes_Resource $resource, $deleteReference = false)
    {
        $returnValue = (bool) false;

        
        
        //delete all values of the property to delete
        if ($deleteReference){
	        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
	        
	    	$modelIds	= implode(',',core_kernel_persistence_smoothsql_SmoothModel::getUpdatableModelIds());
			$query = 'DELETE FROM "statements" WHERE "predicate" = ? AND "modelid" IN ('.$modelIds.')';
	        $returnValue = $dbWrapper->exec($query, array($resource->getUri()));
        }
        $returnValue = core_kernel_persistence_smoothsql_Resource::singleton()->delete($resource, $deleteReference);
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method setRange
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Class class
     * @return core_kernel_classes_Class
     */
    public function setRange( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $class)
    {
        $returnValue = null;

        
        $rangeProp = new core_kernel_classes_Property(RDFS_RANGE, __METHOD__);
        $returnValue = $resource->setPropertyValue($rangeProp, $class->getUri());
        

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
    public function setMultiple( core_kernel_classes_Resource $resource, $isMultiple)
    {
        
    	$multipleProperty = new core_kernel_classes_Property(PROPERTY_MULTIPLE);
        $value = ((bool)$isMultiple) ?  GENERIS_TRUE : GENERIS_FALSE ;
        core_kernel_persistence_smoothsql_Resource::singleton()->removePropertyValues($resource, $multipleProperty);
        core_kernel_persistence_smoothsql_Resource::singleton()->setPropertyValue($resource, $multipleProperty, $value);
        
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
    public function setLgDependent( core_kernel_classes_Resource $resource, $isLgDependent)
    {
        
    	$lgDependentProperty = new core_kernel_classes_Property(PROPERTY_IS_LG_DEPENDENT,__METHOD__);
        $value = ((bool)$isLgDependent) ?  GENERIS_TRUE : GENERIS_FALSE ;
        core_kernel_persistence_smoothsql_Resource::singleton()->removePropertyValues($resource, $lgDependentProperty);
        core_kernel_persistence_smoothsql_Resource::singleton()->setPropertyValue($resource, $lgDependentProperty, $value);
        
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public static function singleton()
    {
        $returnValue = null;

        

		if (core_kernel_persistence_smoothsql_Property::$instance == null){
			core_kernel_persistence_smoothsql_Property::$instance = new core_kernel_persistence_smoothsql_Property();
		}
		$returnValue = core_kernel_persistence_smoothsql_Property::$instance;

        

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
    public function isValidContext( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        

		$returnValue = true;

        

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_smoothsql_Property */

?>