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
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 14.03.2012, 16:36:03 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_smoothsql
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_PersistenceImpl
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/persistence/class.PersistenceImpl.php');

/**
 * include core_kernel_persistence_ResourceInterface
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/persistence/interface.ResourceInterface.php');

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001347-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001347-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001347-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001347-constants end

/**
 * Short description of class core_kernel_persistence_smoothsql_Resource
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_smoothsql
 */
class core_kernel_persistence_smoothsql_Resource
    extends core_kernel_persistence_PersistenceImpl
        implements core_kernel_persistence_ResourceInterface
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
     * Please use getTypes instead
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @deprecated
     * @param  Resource resource
     * @return array
     */
    public function getType( core_kernel_classes_Resource $resource)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001298 begin
        throw new core_kernel_persistence_ProhibitedFunctionException('getType() called, RessourceProxy should have delegated this to getTypes()');
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001298 end

        return (array) $returnValue;
    }

    /**
     * returns an array of types the ressource has
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return array
     */
    public function getTypes( core_kernel_classes_Resource $resource)
    {
        $returnValue = array();

        // section 127-0-1-1--1ee05ee5:13611d6d34c:-8000:000000000000196B begin
		$sqlQuery = 'SELECT "object" FROM "statements" WHERE "subject" = ? and predicate = ?;';
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sth = $dbWrapper->prepare($sqlQuery);
        $sth->execute(array($resource->getUri(), RDF_TYPE));
        while ($row = $sth->fetch()){
            $uri = $row['object'];
            $returnValue[$uri] = new core_kernel_classes_Class($uri);
        }        
        // section 127-0-1-1--1ee05ee5:13611d6d34c:-8000:000000000000196B end

        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  array options
     * @return array
     */
    public function getPropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $options = array())
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129B begin
        $one = isset($options['one']) && $options['one'] == true ? true : false;
		$last = isset($options['last']) && $options['last'] == true ? true : false;
		$session = core_kernel_classes_Session::singleton();
		
    	// Define language if required
		$lang = '';
		$defaultLg = '';
		if (isset($options['lg'])){
			$lang = $options['lg'];
		}
		else{
			$lang = $session->getDataLanguage();
			$defaultLg = ' OR "l_language" = \''.DEFAULT_LANG.'\' ';
		}
		
        $session = core_kernel_classes_Session::singleton();
       	$modelIds = implode(',',array_keys($session->getLoadedModels()));
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
    	
        $query =  'SELECT "object", "l_language"
        			FROM "statements" 
		    		WHERE "subject" = ? 
		    		AND "predicate" = ?
					AND ( "l_language" = ? OR "l_language" = \'\' '.$defaultLg.')
		    		AND "modelID" IN ('.$modelIds.')';
        
    	// Select first
		if($one){
			$query .= ' ORDER BY "id" DESC';
			$query = $dbWrapper->limitStatement($query, 1, 0);
			$sth = $dbWrapper->prepare($query);
			$result = $sth->execute(array($resource->getUri(), $property->getUri(), $lang));
		}
		// Select Last
		else if($last){
			$query .= ' ORDER BY "id" ASC';
			$query = $dbWrapper->limitStatement($query, 1, 0);
			$sth = $dbWrapper->prepare($query);
			$result = $sth->execute(array($resource->getUri(), $property->getUri(), $lang));
		}
		// Select All
		else{
			$sth = $dbWrapper->prepare($query);
			$result = $sth->execute(array($resource->getUri(), $property->getUri(), $lang));
		}
        
		// Treat the query result
        if ($result == true) {
        	// If a language has been defined, do not filter result by language
        	if(isset($options['lg'])){
		    	while ($row = $sth->fetch()){
					$returnValue[] = $row['object'];
				}
        	} 
        	// Filter result by language and return one set of values (User language in top priority, default language in second and the fallback language (null) in third)
        	else {
        		 $returnValue = core_kernel_persistence_smoothsql_Utils::filterByLanguage($sth->fetchAll(), 'l_language');
        	}
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129B end

        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValuesCollection
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesCollection( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129F begin
        
        $returnValue = new core_kernel_classes_ContainerCollection($resource);
        $propertiesValues = $resource->getAllPropertyValues($property);
        $returnValue->sequence = $propertiesValues;
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129F end

        return $returnValue;
    }

    /**
     * Short description of method getOnePropertyValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  boolean last
     * @return core_kernel_classes_Container
     */
    public function getOnePropertyValue( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $last = false)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012A3 begin
        
    	$options = array(
			'forceDefaultLg' => true
		);  
		if($last){
			$options['last'] = true;
		}else{
			$options['one'] = true;
		}

		$value = $resource->getAllPropertyValues($property, $options);
		if (count($value)){
			$returnValue = $value[0];
		}
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012A3 end

        return $returnValue;
    }

    /**
     * Short description of method getPropertyValuesByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string lg
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $lg)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012A9 begin
        
        $options = array (
        	'lg' => $lg
        );
        
        $returnValue = new core_kernel_classes_ContainerCollection($resource);
        $propertiesValues = $resource->getAllPropertyValues($property, $options);
        $returnValue->sequence = $propertiesValues;
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012A9 end

        return $returnValue;
    }

    /**
     * Short description of method setPropertyValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string object
     * @param  string lg
     * @return boolean
     */
    public function setPropertyValue( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $object, $lg = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012AE begin
        
        $object  = $object instanceof core_kernel_classes_Resource ? $object->getUri() : (string) $object;
    	$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
        $session 	= core_kernel_classes_Session::singleton();
        $localNs 	= common_ext_NamespaceManager::singleton()->getLocalNamespace();
        $mask		= 'yyy[admin,administrators,authors]';	//now it's the default right mode
        $lang = "";
        // Define language if required
        if ($property->isLgDependent()){
        	if ($lg!=null){
        		$lang = $lg;
        	} else {
        		$lang = $session->getDataLanguage();
        	}
        }
        
        $query = 'INSERT INTO statements ("modelID", "subject", "predicate", "object", "l_language", "author", "stread", "stedit", "stdelete", "epoch")
        			VALUES  (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP);';

        $returnValue = $dbWrapper->exec($query, array(
       		$localNs->getModelId(),
       		$resource->getUri(),
       		$property->getUri(),
       		$object,
       		$lang,
       		$session->getUserUri(),
       		$mask,
       		$mask,
       		$mask
        ));
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012AE end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setPropertiesValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  array properties
     * @return boolean
     */
    public function setPropertiesValues( core_kernel_classes_Resource $resource, $properties)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012B3 begin

    	if(is_array($properties)){
        	if(count($properties) > 0){
        		
	        	$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
	        	$session 	= core_kernel_classes_Session::singleton();
	        	
	        	$localNs 	= common_ext_NamespaceManager::singleton()->getLocalNamespace();
	       		$modelId	= $localNs->getModelId();
	        	$mask		= 'yyy[admin,administrators,authors]';	//now it's the default right mode
	        	$user		= $session->getUserUri();
	       		
	       		$query = 'INSERT INTO "statements" ("modelID","subject","predicate","object","l_language","author","stread","stedit","stdelete","epoch") VALUES ';
	       		
	       		foreach($properties as $propertyUri => $value){
	       			$property = new core_kernel_classes_Property($propertyUri);
	       			$lang 	= ($property->isLgDependent() ? $session->getDataLanguage() : '');
					
					$formatedValues = array();
					if($value instanceof core_kernel_classes_Resource){
						$formatedValues[] = $value->getUri();
					}else if(is_array($value)){
						foreach($value as $val){
							if($val instanceof core_kernel_classes_Resource){
								$formatedValues[] = $val->getUri();
							}else{
								$formatedValues[] = trim($dbWrapper->dbConnector->quote($val), "'\"");
							}
						}
					}else{
						$formatedValues[] = trim($dbWrapper->dbConnector->quote($value), "'\"");
					}
					
					foreach($formatedValues as $object){
						$query .= " ($modelId, '{$resource->getUri()}', '{$property->getUri()}', '{$object}', '{$lang}', '{$user}', '{$mask}','{$mask}','{$mask}', CURRENT_TIMESTAMP),";
					}
	       		}
	       		
	       		$query = substr($query, 0, strlen($query) -1);
	       		$returnValue = $dbWrapper->exec($query);
        	}
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012B3 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setPropertyValueByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string value
     * @param  string lg
     * @return boolean
     */
    public function setPropertyValueByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $value, $lg)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012B7 begin

		$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
        $session 	= core_kernel_classes_Session::singleton();
        $localNs 	= common_ext_NamespaceManager::singleton()->getLocalNamespace();
        $mask		= 'yyy[admin,administrators,authors]';	//now it's the default right mode
        
        $query = 'INSERT INTO "statements" ("modelID","subject","predicate","object","l_language","author","stread","stedit","stdelete","epoch")
        			VALUES  (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP);';

        $returnValue = $dbWrapper->exec($query, array(
       		$localNs->getModelId(),
       		$resource->getUri(),
       		$property->getUri(),
       		$value,
       		($property->isLgDependent() ? $lg : ''),
       		$session->getUserUri(),
       		$mask,
       		$mask,
       		$mask
        ));
		
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012B7 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method removePropertyValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  array options
     * @return boolean
     */
    public function removePropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $options = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012BD begin
        
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		
		// Optional params
        $pattern = isset($options['pattern']) && !is_null($options['pattern']) ? $options['pattern'] : null;
        $like = isset($options['like']) && $options['like'] == true ? true : false;
		
		//build query:
		$query =  'DELETE FROM "statements" WHERE "subject" = ? AND "predicate" = ?';
		
		$conditions = array();
		if(is_string($pattern)){
			if(!is_null($pattern)){
				$searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($pattern, $like);
				$conditions[] = '( "object" '.$searchPattern.' )';
			}
		}else if(is_array($pattern)){
			if(count($pattern) > 0){
				$multiCondition =  "( ";
				foreach($pattern as $i => $patternToken){
					$searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($patternToken, $like);
					if($i > 0) {
                        $multiCondition .= " OR ";
                    }
					$multiCondition .= '( "object" '.$searchPattern.' )';
				}
				$conditions[] = "{$multiCondition} ) ";
			}
		}
			
        foreach($conditions as $i => $additionalCondition){
			$query .= " AND ( {$additionalCondition} ) ";
		}
        
		//be sure the property we try to remove is included in an updatable model
    	$modelIds	= implode(',',array_keys(core_kernel_classes_Session::singleton()->getUpdatableModels()));
		$query .= ' AND "modelID" IN ('.$modelIds.')';
		
        if($property->isLgDependent()){
        	
        	$session = core_kernel_classes_Session::singleton();
        	$query .=  ' AND ("l_language" = \'\' OR "l_language" = ?) ';
        	$returnValue = $dbWrapper->exec($query,array(
	        		$resource->getUri(),
	        		$property->getUri(),
	        		$session->getDataLanguage()
	        ));
        }
        else{
        	
        	$returnValue = $dbWrapper->exec($query,array(
	        		$resource->getUri(),
	        		$property->getUri()
	        ));   
        }
        
        if (!$returnValue){
        	$returnValue = false;
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012BD end

        return (bool) $returnValue;
    }

    /**
     * Short description of method removePropertyValueByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string lg
     * @param  array options
     * @return boolean
     */
    public function removePropertyValueByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $lg, $options = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C1 begin
        
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlQuery = 'DELETE FROM "statements" WHERE "subject" = ? and "predicate" = ? and "l_language" = ?';
        //be sure the property we try to remove is included in an updatable model
    	$modelIds	= implode(',',array_keys(core_kernel_classes_Session::singleton()->getUpdatableModels()));
		$sqlQuery .= ' AND "modelID" IN ('.$modelIds.')';
        
        $returnValue = $dbWrapper->exec($sqlQuery, array (
        	$resource->getUri(),
        	$property->getUri(),
        	$lg
        ));
        
    	if (!$returnValue){
        	$returnValue = false;
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRdfTriples
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRdfTriples( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C6 begin
        
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
	
	     $namespaces = common_ext_NamespaceManager::singleton()->getAllNamespaces();
	     $namespace = $namespaces[substr($resource->getUri(), 0, strpos($resource->getUri(), '#') + 1)];
	
	     $query = 'SELECT * FROM "statements" WHERE "subject" = ? AND "modelID" = ? ORDER BY "predicate"';
	     $result = $dbWrapper->query($query, array(
	    	 $resource->getUri(),
	     	$namespace->getModelId()
	     ));
	
	     $returnValue = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
	     while($statement = $result->fetch()){
	     	$triple = new core_kernel_classes_Triple();
	     	$triple->modelID = $statement["modelID"];
	     	$triple->subject = $statement["subject"];
	     	$triple->predicate = $statement["predicate"];
	     	$triple->object = $statement["object"];
	     	$triple->id = $statement["id"];
	     	$triple->lg = $statement["l_language"];
	     	$triple->readPrivileges = $statement["stread"];
	     	$triple->editPrivileges = $statement["stedit"];
	     	$triple->deletePrivileges = $statement["stdelete"];
	     	$returnValue->add($triple);
	     }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C6 end

        return $returnValue;
    }

    /**
     * Short description of method getUsedLanguages
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return array
     */
    public function getUsedLanguages( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C9 begin
        
    	$sqlQuery = 'SELECT "l_language" FROM "statements" WHERE "subject" = ? AND "predicate" = ? ';
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlResult = $dbWrapper->query($sqlQuery, array (
        	$resource->getUri(),
        	$property->getUri()
        ));
        while ($row = $sqlResult->fetch()){
            $returnValue[] = $row['l_language'];
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C9 end

        return (array) $returnValue;
    }

    /**
     * Short description of method duplicate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  array excludedProperties
     * @return core_kernel_classes_Resource
     */
    public function duplicate( core_kernel_classes_Resource $resource, $excludedProperties = array())
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012CD begin
        
    	$newUri = common_Utils::getNewUri();
    	
    	$collection = $resource->getRdfTriples();
    	if($collection->count() > 0){
    		
    		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
    		
    		$session = core_kernel_classes_Session::singleton();
    		$localNs = common_ext_NamespaceManager::singleton()->getLocalNamespace();
	       	$modelId = $localNs->getModelId();
    		$user = $session->getUserUri();
    		
	    	$insert = 'INSERT INTO "statements" ("modelID", "subject", "predicate", "object", "l_language", "author", "stread", "stedit", "stdelete") VALUES ';
    		foreach($collection->getIterator() as $triple){
    			if(!in_array($triple->predicate, $excludedProperties)){
	    			$insert .= "({$modelId}, '$newUri', '{$triple->predicate}', " . $dbWrapper->quote($triple->object) . ",  '{$triple->lg}', '{$user}', '{$triple->readPrivileges}', '{$triple->editPrivileges}', '{$triple->deletePrivileges}'),"; 
	    		}
	    	}
	    	$insert = substr($insert, 0, strlen($insert) -1);
	    	
	    	
        	if($dbWrapper->exec($insert)){
        		$returnValue = new core_kernel_classes_Resource($newUri);
        	}
    	}
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012CD end

        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete( core_kernel_classes_Resource $resource, $deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012D2 begin
        
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        
    	$modelIds	= implode(',',array_keys(core_kernel_classes_Session::singleton()->getUpdatableModels()));
		$query = 'DELETE FROM "statements" WHERE "subject" = ? AND "modelID" IN ('.$modelIds.')';
        $returnValue = $dbWrapper->exec($query, array($resource->getUri()));

        //if no rows affected return false
        if (!$returnValue){
        	$returnValue = false;
        } 
        else if($deleteReference){
        	$sqlQuery = 'DELETE FROM "statements" WHERE "object" = ? AND "modelID" IN ('.$modelIds.')';
        	$return = $dbWrapper->exec($sqlQuery, array ($resource->getUri()));
        	
        	if ($return !== false){
        		$returnValue = true;
        	}
        }
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012D2 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getLastModificationDate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return core_kernel_persistence_doc_date
     */
    public function getLastModificationDate( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property = null)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012D7 begin
        
    	$sqlQuery = 'SELECT "epoch" FROM "statements" WHERE "subject" = \''. $resource->getUri().'\' ';

        if(!is_null($property) && $property instanceof core_kernel_classes_Property){
            $sqlQuery = $sqlQuery.' AND "predicate" = \''. $property->getUri().'\' ';
        }

        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlResult = $dbWrapper->query($sqlQuery);
		$rows = $sqlResult->fetchAll();
		
        if(!is_null($property) && count($rows) == 0){
            throw new common_Exception("The resource does not have the specified property.");
        }

        foreach ($rows as $row){
            $last = $row['epoch'];
            $lastDate = date_create($last);
            if($returnValue == null ) {
                $returnValue = $lastDate;
            }
            else {
                if($returnValue < $lastDate) {
                    $returnValue = $lastDate;
                }
            }
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012D7 end

        return $returnValue;
    }

    /**
     * Short description of method getLastModificationUser
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return string
     */
    public function getLastModificationUser( core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012DC begin
        
        $sqlQuery = "SELECT author FROM statements WHERE subject = ? and predicate = ?";
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlResult = $dbWrapper->query($sqlQuery, array (
        	$resource->getUri(),
        	RDF_TYPE
        ));
        
        if ($row = $sqlResult->fetch()){
        	$returnValue = $row['author'];	
        }
        else{
        	$returnValue = null;
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012DC end

        return (string) $returnValue;
    }

    /**
     * Short description of method getPropertiesValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  array properties
     * @return array
     */
    public function getPropertiesValues( core_kernel_classes_Resource $resource, $properties)
    {
        $returnValue = array();

        // section 127-0-1-1-77557f59:12fa87873f4:-8000:00000000000014D1 begin
        // check whenever or not properties is empty
        if (count($properties) == 0) {
        	return array();
        }
        
        /*foreach($properties as $property){
        	$returnValue[$property->getUri()] = $this->getPropertyValues($resource, $property);
        }*/
 
    	$predicatesQuery = '';
    	//build the predicate query
       	//$predicatesQuery = implode(',', $properties);
		foreach ($properties as $property) {
			$uri = (is_string($property) ? $property : $property->getUri());
			$returnValue[$uri] = array();
			$predicatesQuery .= ", '" . $uri . "'";
		}
    	$predicatesQuery=substr($predicatesQuery, 1);
        
        $session 	= core_kernel_classes_Session::singleton();
    	$modelIds	= implode(',', array_keys($session->getLoadedModels()));
    	$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
 
    	//the unique sql query
        $query =  'SELECT "predicate", "object", "l_language" 
            FROM "statements" 
            WHERE 
                "subject" = \''.$resource->getUri().'\' 
                AND "predicate" IN ('.$predicatesQuery.')
                AND ("l_language" = \'\' 
                    OR "l_language" = \''.DEFAULT_LANG.'\' 
                    OR "l_language" = \''.$session->getDataLanguage().'\') 
                AND "modelID" IN ('.$modelIds.')';
        
        $result	= $dbWrapper->query($query);
        
        $rows = $result->fetchAll();
        $sortedByLg = core_kernel_persistence_smoothsql_Utils::sortByLanguage($rows, 'l_language');
        $identifiedLg = core_kernel_persistence_smoothsql_Utils::identifyFirstLanguage($sortedByLg);

        foreach($rows as $row){
        	$value = $row['object'];
			$returnValue[$row['predicate']][] = common_Utils::isUri($value)
				? new core_kernel_classes_Resource($value)
				: new core_kernel_classes_Literal($value);
        }
        
        // section 127-0-1-1-77557f59:12fa87873f4:-8000:00000000000014D1 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Class class
     * @return boolean
     */
    public function setType( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:0000000000001548 begin
        
		$returnValue = $this->setPropertyValue($resource, new core_kernel_classes_Property(RDF_TYPE), $class);
        
        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:0000000000001548 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method removeType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Class class
     * @return boolean
     */
    public function removeType( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:000000000000154C begin
        
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $query =  'DELETE FROM "statements" 
		    		WHERE "subject" = ? AND "predicate" = ? AND "object" = ?';
        
        //be sure the property we try to remove is included in an updatable model
    	$modelIds	= implode(',',array_keys(core_kernel_classes_Session::singleton()->getUpdatableModels()));
		$query .= ' AND "modelID" IN ('.$modelIds.')';
        
        $returnValue = $dbWrapper->exec($query,array(
        	$resource->getUri(),
        	RDF_TYPE,
        	$class->getUri()
        ));
        
        $returnValue = true;
        
        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:000000000000154C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001354 begin
        
        if (core_kernel_persistence_smoothsql_Resource::$instance == null){
        	core_kernel_persistence_smoothsql_Resource::$instance = new core_kernel_persistence_smoothsql_Resource();
        }
        $returnValue = core_kernel_persistence_smoothsql_Resource::$instance;
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001354 end

        return $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isValidContext( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F4B begin
        
        $returnValue = true;
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F4B end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_smoothsql_Resource */

?>