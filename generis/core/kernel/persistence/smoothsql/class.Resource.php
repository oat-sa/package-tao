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
 * Short description of class core_kernel_persistence_smoothsql_Resource
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 
 */
class core_kernel_persistence_smoothsql_Resource
    extends core_kernel_persistence_PersistenceImpl
        implements core_kernel_persistence_ResourceInterface
{

    /**
     * @var core_kernel_persistence_smoothsql_SmoothModel
     */
    private $model;
    
    public function __construct(core_kernel_persistence_smoothsql_SmoothModel $model) {
        $this->model = $model;
    }
    
    protected function getModel() {
        return $this->model;
    }
    
    /**
     * @return common_persistence_SqlPersistence
     */
    protected function getPersistence() {
        return $this->model->getPersistence();
    }
    
    protected function getModelReadSqlCondition() {
        return 'modelid IN ('.implode(',', $this->model->getReadableModels()).')';
    }
    
    protected function getModelWriteSqlCondition() {
        return 'modelid IN ('.implode(',',$this->model->getWritableModels()).')';
    }
    
    protected function getNewTripleModelId() {
        return $this->model->getNewTripleModelId();
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

        
		$sqlQuery = 'SELECT object FROM statements WHERE subject = ? and predicate = ?';
        $sth = $this->getPersistence()->query($sqlQuery,array($resource->getUri(), RDF_TYPE));

        while ($row = $sth->fetch()){
            $uri = $this->getPersistence()->getPlatForm()->getPhpTextValue($row['object']);
            $returnValue[$uri] = new core_kernel_classes_Class($uri);
        }        
        

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

        
        $one = isset($options['one']) && $options['one'] == true ? true : false;
        if (isset($options['last'])) {
            throw new core_kernel_persistence_Exception('Option \'last\' no longer supported');
        }
		$platform = $this->getPersistence()->getPlatForm();
		
    	// Define language if required
		$lang = '';
		$defaultLg = '';
		if (isset($options['lg'])){
			$lang = $options['lg'];
		}
		else{
			$lang = \common_session_SessionManager::getSession()->getDataLanguage();
			$defaultLg = ' OR l_language = '.$this->getPersistence()->quote(DEFAULT_LANG).' ';
		}
		
        $query =  'SELECT object, l_language
        			FROM statements 
		    		WHERE subject = ? 
		    		AND predicate = ?
					AND ( l_language = ? OR ' .$platform->isNullCondition('l_language') .$defaultLg.')
		    		AND '.$this->getModelReadSqlCondition();
        
    	// Select first
		if($one){
			$query .= ' ORDER BY id DESC';
			$query = $platform->limitStatement($query, 1, 0);
			$result = $this->getPersistence()->query($query,array($resource->getUri(), $property->getUri(), $lang));
		}
		// Select All
		else{
			$result = $this->getPersistence()->query($query,array($resource->getUri(), $property->getUri(), $lang));
		}
        
		// Treat the query result
        if ($result == true) {
        	// If a language has been defined, do not filter result by language
        	if(isset($options['lg'])){
		    	while ($row = $result->fetch()){
					$returnValue[] = $this->getPersistence()->getPlatForm()->getPhpTextValue($row['object']);
				}
        	} 
        	// Filter result by language and return one set of values (User language in top priority, default language in second and the fallback language (null) in third)
        	else {
        		 $returnValue = core_kernel_persistence_smoothsql_Utils::filterByLanguage($this->getPersistence(), $result->fetchAll(), 'l_language');
        	}
        }
        
        

        return (array) $returnValue;
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

        
        
        $options = array (
        	'lg' => $lg
        );
        
        $returnValue = new core_kernel_classes_ContainerCollection($resource);
        foreach ($this->getPropertyValues($resource, $property, $options) as $value){
            $returnValue->add(common_Utils::toResource($value));
        }
        
        

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

        
        
        $object  = $object instanceof core_kernel_classes_Resource ? $object->getUri() : (string) $object;
    	$platform = $this->getPersistence()->getPlatForm();
        $mask		= 'yyy[admin,administrators,authors]';	//now it's the default right mode
        $lang = "";
        // Define language if required
        if ($property->isLgDependent()){
        	if ($lg!=null){
        		$lang = $lg;
        	} else {
        		$lang = \common_session_SessionManager::getSession()->getDataLanguage();
        	}
        }
        
        $query = 'INSERT INTO statements (modelid, subject, predicate, object, l_language, author,epoch)
        			VALUES  (?, ?, ?, ?, ?, ? , ?)';

        $returnValue = $this->getPersistence()->exec($query, array(
       		$this->getNewTripleModelId(),
       		$resource->getUri(),
       		$property->getUri(),
       		$object,
       		$lang,
       		\common_session_SessionManager::getSession()->getUserUri(),
//        		$mask,
//        		$mask,
//        		$mask,
            $platform->getNowExpression()
        ));
        
        

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

        

    	if(is_array($properties)){
        	if(count($properties) > 0){
        		
	        	$platform = $this->getPersistence()->getPlatForm();
	        	$mask		= 'yyy[admin,administrators,authors]';	//now it's the default right mode
	        	$user		= common_session_SessionManager::isAnonymous()
                    ? $platform->getNullString()
                    : $this->getPersistence()->quote(common_session_SessionManager::getSession()->getUser()->getIdentifier());
	       		
	      
	        	$multipleInsertQueryHelper = $platform->getMultipleInsertsSqlQueryHelper();
	        	
	       		//$query = 'INSERT INTO "statements" ("modelid","subject","predicate","object","l_language","author","stread","stedit","stdelete","epoch") VALUES ';
				$columns = array(
	       							"modelid",
	       							"subject",
	       							"predicate",
	       							"object",
	       							"l_language",
	       							"author",
// 	       							"stread",
// 	       							"stedit",
// 	       							"stdelete",
				                    "epoch"
				);
	       		$query = $multipleInsertQueryHelper->getFirstStaticPart('statements', $columns);

	       		foreach($properties as $propertyUri => $value){
	       			$property = new core_kernel_classes_Property($propertyUri);
	       			$lang 	= ($property->isLgDependent() ? $this->getPersistence()->quote(\common_session_SessionManager::getSession()->getDataLanguage()) : $platform->getNullString()  );

					$formatedValues = array();
					if($value instanceof core_kernel_classes_Resource){
						$formatedValues[] = $this->getPersistence()->quote($value->getUri());
					}else if(is_array($value)){
						foreach($value as $val){
							if($val instanceof core_kernel_classes_Resource){
								$formatedValues[] = $this->getPersistence()->quote($val->getUri());
							}else{
								$formatedValues[] = $this->getPersistence()->quote($val);
							}
						}
					}else{
						if($value == null){
							$formatedValues[] = $platform->getNullString();
						}
						else {
							$formatedValues[] = $this->getPersistence()->quote($value);
						}
					}
					
					foreach($formatedValues as $object){
						$query .= $multipleInsertQueryHelper->getValuePart('statements', $columns,
								array(
										"modelid" => $this->getNewTripleModelId(),
										"subject" => $this->getPersistence()->quote($resource->getUri()),
										"predicate"=> $this->getPersistence()->quote($property->getUri()),
										"object" => $object,
										"l_language" => $lang,
										"author" => $user,
// 										"stread" => $this->getPersistence()->quote($mask),
// 										"stedit" => $this->getPersistence()->quote($mask),
// 										"stdelete" => $this->getPersistence()->quote($mask),
								     "epoch" =>	$this->getPersistence()->quote($platform->getNowExpression() )
								));
					}
	       		}
	       		
	       		$query = substr($query, 0, strlen($query) -1);
	       		$query .= $multipleInsertQueryHelper->getEndStaticPart();
	       		$returnValue = $this->getPersistence()->exec($query);
        	}
        }
        
        

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

        

		$platform = $this->getPersistence()->getPlatForm();
		$userId     = common_session_SessionManager::isAnonymous()
    		? null : \common_session_SessionManager::getSession()->getUser()->getIdentifier();
        $mask		= 'yyy[admin,administrators,authors]';	//now it's the default right mode
        
        $query = 'INSERT INTO statements (modelid,subject,predicate,object,l_language,author,epoch)
        			VALUES  (?, ?, ?, ?, ?, ?, ?)';

        $returnValue = $this->getPersistence()->exec($query, array(
       		$this->getNewTripleModelId(),
       		$resource->getUri(),
       		$property->getUri(),
       		$value,
       		($property->isLgDependent() ? $lg : ''),
       		$userId,
//        		$mask,
//        		$mask,
//        		$mask,
            $platform->getNowExpression()
        ));
		
        

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

		// Optional params
        $pattern = isset($options['pattern']) && !is_null($options['pattern']) ? $options['pattern'] : null;
        $like = isset($options['like']) && $options['like'] == true ? true : false;
		
		//build query:
		$query =  'DELETE FROM statements WHERE subject = ? AND predicate = ?';
		$objectType = $this->getPersistence()->getPlatForm()->getObjectTypeCondition();
		$conditions = array();
		if(is_string($pattern)){
			if(!is_null($pattern)){
				$searchPattern = core_kernel_persistence_smoothsql_Utils::buildSearchPattern($this->getPersistence(), $pattern, $like);
				$conditions[] = '( '.$objectType . ' ' .$searchPattern.' )';
			}
		}else if(is_array($pattern)){
			if(count($pattern) > 0){
				$multiCondition =  "( ";
				foreach($pattern as $i => $patternToken){
					$searchPattern = core_kernel_persistence_smoothsql_Utils::buildSearchPattern($this->getPersistence(), $patternToken, $like);
					if($i > 0) {
                        $multiCondition .= " OR ";
                    }
					$multiCondition .= '('.$objectType. ' ' .$searchPattern.' )';
				}
				$conditions[] = "{$multiCondition} ) ";
			}
		}
			
        foreach($conditions as $i => $additionalCondition){
			$query .= " AND ( {$additionalCondition} ) ";
		}
        
		//be sure the property we try to remove is included in an updatable model
		$query .= ' AND '.$this->getModelWriteSqlCondition();
		
        if($property->isLgDependent()){
        	
        	$query .=  ' AND (' . $this->getPersistence()->getPlatForm()->isNullCondition('l_language') . ' OR l_language = ?) ';
        	$returnValue = $this->getPersistence()->exec($query,array(
	        		$resource->getUri(),
	        		$property->getUri(),
	        		\common_session_SessionManager::getSession()->getDataLanguage()
	        ));
        }
        else{
        	$returnValue = $this->getPersistence()->exec($query,array(
	        		$resource->getUri(),
	        		$property->getUri()
	        ));   
        }
        
        if (!$returnValue){
        	$returnValue = false;
        }
        
        

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

        $sqlQuery = 'DELETE FROM statements WHERE subject = ? and predicate = ? and l_language = ?';
        //be sure the property we try to remove is included in an updatable model
		$sqlQuery .= ' AND '.$this->getModelWriteSqlCondition();
        
        $returnValue = $this->getPersistence()->exec($sqlQuery, array (
        	$resource->getUri(),
        	$property->getUri(),
        	$lg
        ));
        
    	if (!$returnValue){
        	$returnValue = false;
        }
        
        

        return (bool) $returnValue;
    }

    /**
     * returns the triples having as subject the current resource
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  core_kernel_classes_Resource resource
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRdfTriples( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;
        
        $query = 'SELECT * FROM statements WHERE subject = ? AND '.$this->getModelReadSqlCondition().' ORDER BY predicate';
        $result = $this->getPersistence()->query($query, array($resource->getUri()));
        
        $returnValue = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
        while ($statement = $result->fetch()) {
            $triple = new core_kernel_classes_Triple();
            $triple->modelid = $statement["modelid"];
            $triple->subject = $statement["subject"];
            $triple->predicate = $statement["predicate"];
            $triple->object = $statement["object"];
            $triple->id = $statement["id"];
            $triple->lg = $statement["l_language"];
            $returnValue->add($triple);
        }

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

        
        
    	$sqlQuery = 'SELECT l_language FROM statements WHERE subject = ? AND predicate = ? ';
        $sqlResult = $this->getPersistence()->query($sqlQuery, array (
        	$resource->getUri(),
        	$property->getUri()
        ));
        while ($row = $sqlResult->fetch()) {
            if (!empty($row['l_language'])) {
                $returnValue[] = $row['l_language'];
            }
        }
        
        

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

        
        
    	$newUri = common_Utils::getNewUri();
    	
    	$collection = $this->getRdfTriples($resource);
    	if($collection->count() > 0){
    		
    		$platform = $this->getPersistence()->getPlatForm();
    		$multipleInsertQueryHelper = $platform->getMultipleInsertsSqlQueryHelper();
    		
        	$user    = common_session_SessionManager::isAnonymous()
        	   ? $platform->getNullString()
        	   : $this->getPersistence()->quote(\common_session_SessionManager::getSession()->getUser()->getIdentifier());
	       		
    		   		
	    	$columns = array(
	    			"modelid",
	    			"subject",
	    			"predicate",
	    			"object",
	    			"l_language",
	    			"author",
                    "epoch"
	    	);
	    	$query = $multipleInsertQueryHelper->getFirstStaticPart('statements', $columns);
    		
    		foreach($collection->getIterator() as $triple){
    			if(!in_array($triple->predicate, $excludedProperties)){
	    			$query .= $multipleInsertQueryHelper->getValuePart('statements', $columns,
	    					array(
	    							"modelid" => $this->getNewTripleModelId(),
	    							"subject" => $this->getPersistence()->quote($newUri),
	    							"predicate"=> $this->getPersistence()->quote($triple->predicate),
	    							"object" => $triple->object == null ? $platform->getNullString() : $this->getPersistence()->quote($triple->object),
	    							"l_language" => $triple->lg == null ? $platform->getNullString() : $this->getPersistence()->quote($triple->lg),
	    							"author" => $user,
     					            "epoch" => $this->getPersistence()->quote($platform->getNowExpression())
	    					));
    			}
	    	}
	    	
	    	$query = substr($query, 0, strlen($query) -1);
	    	$query .= $multipleInsertQueryHelper->getEndStaticPart();
	    	
        	if($this->getPersistence()->exec($query)){
        		$returnValue = new core_kernel_classes_Resource($newUri);
        	}
    	}
        
        

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

		$query = 'DELETE FROM statements WHERE subject = ? AND '.$this->getModelWriteSqlCondition();
        $returnValue = $this->getPersistence()->exec($query, array($resource->getUri()));

        //if no rows affected return false
        if (!$returnValue){
        	$returnValue = false;
        } 
        else if($deleteReference){
        	$sqlQuery = 'DELETE FROM statements WHERE ' . $this->getPersistence()->getPlatForm()->getObjectTypeCondition() . ' = ? AND '.$this->getModelWriteSqlCondition();
        	$return = $this->getPersistence()->exec($sqlQuery, array ($resource->getUri()));
        	
        	if ($return !== false){
        		$returnValue = true;
        	}
        }
        

        return (bool) $returnValue;
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
			$predicatesQuery .= ", " . $this->getPersistence()->quote($uri) ;
		}
    	$predicatesQuery=substr($predicatesQuery, 1);

 		$platform = $this->getPersistence()->getPlatForm();
    	//the unique sql query
        $query =  'SELECT predicate, object, l_language 
            FROM statements 
            WHERE 
                subject = '.$this->getPersistence()->quote($resource->getUri()).' 
                AND predicate IN ('.$predicatesQuery.')
                AND ('. $platform->isNullCondition('l_language') . 
                    ' OR l_language = '.$this->getPersistence()->quote(DEFAULT_LANG). 
                    ' OR l_language = '.$this->getPersistence()->quote(\common_session_SessionManager::getSession()->getDataLanguage()).') 
                AND '.$this->getModelReadSqlCondition();
        $result	= $this->getPersistence()->query($query);
        
        $rows = $result->fetchAll();
        $sortedByLg = core_kernel_persistence_smoothsql_Utils::sortByLanguage($this->getPersistence(), $rows, 'l_language');
        $identifiedLg = core_kernel_persistence_smoothsql_Utils::identifyFirstLanguage($sortedByLg);

        foreach($rows as $row){
        	$value = $platform->getPhpTextValue($row['object']);
			$returnValue[$row['predicate']][] = common_Utils::isUri($value)
				? new core_kernel_classes_Resource($value)
				: new core_kernel_classes_Literal($value);
        }
        
        

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

        
        
		$returnValue = $this->setPropertyValue($resource, new core_kernel_classes_Property(RDF_TYPE), $class);
        
        

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

        
        
        $query =  'DELETE FROM statements 
		    		WHERE subject = ? AND predicate = ? AND '. $this->getPersistence()->getPlatForm()->getObjectTypeCondition() .' = ?';
        
        //be sure the property we try to remove is included in an updatable model
		$query .= ' AND '.$this->getModelWriteSqlCondition();
        
        $returnValue = $this->getPersistence()->exec($query,array(
        	$resource->getUri(),
        	RDF_TYPE,
        	$class->getUri()
        ));
        
        $returnValue = true;
        
        

        return (bool) $returnValue;
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
        return true;
    }

}
