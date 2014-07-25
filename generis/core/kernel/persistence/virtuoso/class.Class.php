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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */


/**
 * Short description of class core_kernel_persistence_virtuoso_Class
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_virtuoso
 */
class core_kernel_persistence_virtuoso_Class
    extends core_kernel_persistence_PersistenceImpl
        implements core_kernel_persistence_ClassInterface
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
     * Short description of method getSubClasses
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getSubClasses( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014EB begin
        
        list($NS, $ID) = explode('#', $resource->getUri());
        
        if (isset($ID) && !empty($ID)) {

                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();

                $query = 'PREFIX classNS: <' . $NS . '#>  SELECT ?s WHERE {?s rdfs:subClassOf classNS:' . $ID . '}';
                
                $resultArray = $virtuoso->execQuery($query);
                $count = count($resultArray);
                for ($i = 0; $i < $count; $i++) {
                        if (isset($resultArray[$i][0])) {
                                $subClass = new core_kernel_classes_Class($resultArray[$i][0]);
                                $returnValue[$subClass->getUri()] = $subClass;
                                if($recursive === true){
                                        $subSubClasses = $subClass->getSubClasses(true);
                                        $returnValue = array_merge($returnValue , $subSubClasses);
                                }
                        }
                }
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014EB end

        return (array) $returnValue;
    }

    /**
     * Short description of method isSubClassOf
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Class parentClass
     * @return boolean
     */
    public function isSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $parentClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F0 begin
        list($NS, $ID) = explode('#', $resource->getUri());
        list($parentNS, $parentID) = explode('#', $parentClass->getUri());
        if (isset($ID) && !empty($ID)) {

                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                
                $query = 'PREFIX classNS: <' . $NS . '#> 
                        PREFIX parentNS: <' . $parentNS . '#> 
                        ASK {classNS:' . $ID . ' rdfs:subClassOf parentNS:' . $parentID . '}';
                //TODO: check issue: only one triple allowed for an identical SPO for a given language-> issue with multiple identical objects for SP (i.e. parallel branch for wfEngine)
                $returnValue = $virtuoso->execQuery($query, 'Boolean');
        }
        
        if (!$returnValue) {
                $parentSubClasses = $parentClass->getSubClasses(true);
                foreach ($parentSubClasses as $subClass) {
                        if ($subClass->getUri() == $resource->getUri()) {
                                $returnValue = true;
                                break;
                        }
                }
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getParentClasses
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getParentClasses( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F5 begin
        
        list($NS, $ID) = explode('#', $resource->getUri());
        if (isset($ID) && !empty($ID)) {

                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                //TODO: check why or condiiton with rdf:type?? in the smooth impl
                $query = 'PREFIX classNS: <' . $NS . '#>  SELECT ?o WHERE {classNS:' . $ID . ' rdfs:subClassOf ?o}';
                
                $resultArray = $virtuoso->execQuery($query);
                $count = count($resultArray);
                for ($i = 0; $i < $count; $i++) {
                        if (isset($resultArray[$i][0])) {
                                $parentClass = new core_kernel_classes_Class($resultArray[$i][0]);
                                $returnValue[$parentClass->getUri()] = $parentClass ;
                                if($recursive == true && $parentClass->getUri() != RDFS_CLASS && $parentClass->getUri() != RDFS_RESOURCE){
                                        $recursiveParents = $parentClass->getParentClasses(true);
                                        $returnValue = array_merge($returnValue, $recursiveParents);
                                }
                        }
                }
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F5 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getProperties
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getProperties( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014FA begin
        
        list($NS, $ID) = explode('#', $resource->getUri());
        if (isset($ID) && !empty($ID)) {

                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                //TODO: check why or condiiton with rdf:type?? in the smooth impl
                $query = 'PREFIX classNS: <' . $NS . '#>  SELECT ?s WHERE {?s rdfs:domain classNS:' . $ID . '}';

                $resultArray = $virtuoso->execQuery($query);
                $count = count($resultArray);
                for ($i = 0; $i < $count; $i++) {
                        if (isset($resultArray[$i][0])) {
                                $property = new core_kernel_classes_Property($resultArray[$i][0]);
                                $returnValue[$property->getUri()] = $property;
                        }
                }
                
                if($recursive == true) {
			$parentClasses = $resource->getParentClasses(true);
			foreach ($parentClasses as $parent) {
				if($parent->getUri() != RDFS_CLASS) {
					$returnValue = array_merge($returnValue, $parent->getProperties(true));
				}
			}
		}
        }
                
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014FA end

        return (array) $returnValue;
    }

    /**
     * Short description of method getInstances
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @param  array params
     * @return array
     */
    public function getInstances( core_kernel_classes_Resource $resource, $recursive = false, $params = array())
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001500 begin
        list($NS, $ID) = explode('#', $resource->getUri());
        if(isset($ID) && !empty($ID)){
                
                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                
                $query = 'PREFIX classNS: <'.$NS.'#>  SELECT ?s WHERE {?s rdf:type classNS:'.$ID.'} ';
                
                if(isset($params['limit'])){
                        $offset = 0;
                        $limit = intval($params['limit']);
                        if ($limit==0){
                                $limit = 1000000;
                                
                        }
                        $query .= " LIMIT ".$limit;
                        
                        if(isset($params['offset'])){
                                $offset = intval($params['offset']);
                                $query .= " OFFSET ".$offset;
                        }
                }
        
                $resultArray = $virtuoso->execQuery($query);
                $count = count($resultArray);
                for($i = 0; $i<$count; $i++){
                        if(isset($resultArray[$i][0])){
                                
                                $instance = new core_kernel_classes_Resource($resultArray[$i][0]);

                                $returnValue[$instance->getUri()] = $instance ;

                                //In case of a meta class, subclasses of instances may be returned*/
                                if (($instance->getUri()!=RDFS_CLASS)
                                && ($resource->getUri() == RDFS_CLASS)
                                && ($instance->getUri()!=RDFS_RESOURCE)) {

                                        $instanceClass = new core_kernel_classes_Class($instance->getUri());
                                        $subClasses = $instanceClass->getSubClasses(true);

                                        foreach($subClasses as $subClass) {
                                                $returnValue[$subClass->getUri()] = $subClass;
                                        }
                                }
                        }
                }
                
                if($recursive == true){
                        $subClasses = $resource->getSubClasses(true);
                        foreach ($subClasses as $subClass){
                                $returnValue = array_merge($returnValue, $subClass->getInstances(true, $params));
                        }
                }
                
        }
                
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001500 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setInstance
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Resource instance
     * @return core_kernel_classes_Resource
     */
    public function setInstance( core_kernel_classes_Resource $resource,  core_kernel_classes_Resource $instance)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001506 begin
        
        $newInstance = $instance->duplicate();
        
        if(!is_null($newInstance)){
                if($newInstance->setType($resource)){
                        $returnValue = $newInstance; 
                }
        }
                
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001506 end

        return $returnValue;
    }

    /**
     * Short description of method setSubClassOf
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Class iClass
     * @return boolean
     */
    public function setSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $iClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000150F begin
        
        $subClassOf = new core_kernel_classes_Property(RDFS_SUBCLASSOF);
        $returnValue = $resource->setPropertyValue($subClassOf,$iClass->getUri());
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000150F end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setProperty
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return boolean
     */
    public function setProperty( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001512 begin
        
        $domain = new core_kernel_classes_Property(RDFS_DOMAIN,__METHOD__);
        $instanceProperty = new core_kernel_classes_Resource($property->getUri(),__METHOD__);
        $returnValue = $instanceProperty->setPropertyValue($domain, $resource->getUri());
                
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001512 end

        return (bool) $returnValue;
    }

    /**
     * Should not be called by application code, please use
     * instead
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Resource
     */
    public function createInstance( core_kernel_classes_Resource $resource, $label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F27 begin
        
        if($uri == ''){
                $subject = common_Utils::getNewUri();
        }
        else {
                //$uri should start with # and be well formed
                if ($uri[0]=='#'){
                        $modelUri = rtrim(common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri(), '#');
                        $subject = $modelUri . $uri;
                } else {
                        $subject = $uri;
                }
        }
        
        list($NS, $ID) = explode('#', $subject);
        list($classNS, $classID) = explode('#', $resource->getUri());
        if(!empty($ID) && !empty($classID)){
                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                $query = '
                        PREFIX resourceNS: <'.$NS.'#>
                        PREFIX classNS: <'.$classNS.'#>
                        INSERT INTO <'.$virtuoso->getCurrentGraph().'> {resourceNS:'.$ID.' rdf:type classNS:'.$classID.'}';
                
                if($virtuoso->execQuery($query, 'Boolean')){
                        $returnValue = new core_kernel_classes_Resource($subject,__METHOD__);
                        if ($label != '') {
                                $returnValue->setLabel($label);
                        }
                        if( $comment != '') {
                                $returnValue->setComment($comment);
                        }
                }else{
                        throw new core_kernel_persistence_virtuoso_Exception("cannot create instance of {$resource->getLabel()} ({$resource->getUri()})");
                }
        }
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F27 end

        return $returnValue;
    }

    /**
     * Short description of method createSubClass
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function createSubClass( core_kernel_classes_Resource $resource, $label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 begin
        
        $class = new core_kernel_classes_Class(RDFS_CLASS,__METHOD__);
        $intance = $class->createInstance($label, $comment, $uri);
        $returnValue = new core_kernel_classes_Class($intance->getUri());
        $returnValue->setSubClassOf($resource);
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 end

        return $returnValue;
    }

    /**
     * Short description of method createProperty
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  boolean isLgDependent
     * @return core_kernel_classes_Property
     */
    public function createProperty( core_kernel_classes_Resource $resource, $label = '', $comment = '', $isLgDependent = false)
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F3C begin
        
        $property = new core_kernel_classes_Class(RDF_PROPERTY,__METHOD__);
        $propertyInstance = $property->createInstance($label,$comment);
        $returnValue = new core_kernel_classes_Property($propertyInstance->getUri(),__METHOD__);
        $returnValue->setLgDependent($isLgDependent);

        if (!$resource->setProperty($returnValue)){
                throw new common_Exception('problem creating property');
        }
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F3C end

        return $returnValue;
    }

    /**
     * Short description of method searchInstances
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function searchInstances( core_kernel_classes_Resource $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        // section 10-13-1--128--26678bb4:12fbafcb344:-8000:00000000000014F0 begin
        
        if(count($propertyFilters) == 0){
		return $returnValue;
        }
        
        //add the type check to the filters
        $propertyFilters[RDF_TYPE] = $resource->getUri();
        
        list($NS, $ID) = explode('#', $resource->getUri());
        if(!empty($ID)){
                
                $session = core_kernel_classes_Session::singleton();
                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                
                $prefixes =  array($NS => 'classNS');//not really useful but set for information only
                $filters = array();
                $objects = array();
                
                $lg = '';
                if(isset($options['lang'])){
                        $lg = $virtuoso->filterLanguageValue($options['lang']);
                }
                
                $like = true;
                if(isset($options['like'])){
                        $like = ($options['like'] === true);
                }
                
                $conditions = array();
                foreach($propertyFilters as $propertyUri => $pattern){

					list($propNS, $propID) = explode('#', $propertyUri);
					if(!empty($propID)){

						if(!isset($prefixes[$propNS])){
								$prefixes[$propNS] = 'NS'.count($prefixes);
						}

						if (is_string($pattern)) {
							if (!empty($pattern)) {
								$o = '?o'.count($objects);
								$objects[] = $o;

								$object = trim($pattern);

								if(common_Utils::isUri($object)){
										//if it is a uri, ignore "like" and "lang" options:
										list($objectNS, $objectID) = explode('#', $object);
										if(!empty($objectID)){
												if(!isset($prefixes[$objectNS])){
														$prefixes[$objectNS] = 'NS'.count($prefixes);
												}
												$conditions[] = $prefixes[$propNS].':'.$propID.' '.$prefixes[$objectNS].':'.$objectID.' ; '; 
										}
								}else{
										if ($like) {
												$filters[] = 'regex(str('.$o.'), "'.$virtuoso->escapeRegex($object, 'regex').'")';
												if (!empty($lg)) {//&& !common_Utils::isUri($object)
														$filters[] = 'langMatches(lang(' . $o . '),"' . $lg . '")';
												}
												$conditions[] = $prefixes[$propNS].':'.$propID.' '.$o.' ; ';
										}else{
												$object = '"'.$virtuoso->escape($object).'"';
												$object .= empty($lg)?'':'@'.$lg;
												$conditions[] = $prefixes[$propNS].':'.$propID.' '.$object.' ; ';
										}
								}
						}
					} else if (is_array($pattern)) {
						if (count($pattern) > 0) {
								$o = '?o'.count($objects);
								$objects[] = $o;

								$validLanguageMatching = true;
								$multiCondition = '(';
								foreach ($pattern as $i => $patternToken) {
										if ($i > 0) {
												$multiCondition .= " || ";
										}

										$object = trim($patternToken);

										if(!$validLanguageMatching && common_Utils::isUri($object)) {
										    $validLanguageMatching = false;//no resource available for language dependent check
										}

										if (!$like) {
												$object = preg_match('/^\^/', $object)? $object : '^'.$object;
												$object = preg_match('/\$$/', $object)? $object : $object.'$';
										}

										$multiCondition .= 'regex(str('.$o.'), "'.$virtuoso->escapeRegex($object, 'regex').'")';
								}

								if(!empty($lg) && $validLanguageMatching){
										$filters[] = 'langMatches(lang('.$o.'),'.$lg.')';
								}

								$filters[] = $multiCondition.')';

								$conditions[] = $prefixes[$propNS].':'.$propID.' '.$o.' ; ';
							}
						}
					}
                }
                if(count($conditions) == 0){
					return $returnValue;
                }
                
                //start building query:
                $query = '';
                
                //insert prefixes:
                foreach($prefixes as $ns => $alias){
					$query .= '
						PREFIX '.$alias.':<'.$ns.'#> ';
                }
                
                $from = '';
//                $taoNS = array();
//                preg_match_all('/http:\/\/www\.tao\.lu\/(middleware|Ontologies)\/(.*).rdf/i', $NS, $taoNS);
//                if(!empty($taoNS[0]) && !empty($taoNS[1]) && !empty($taoNS[2])){
//                        $tao_ns = strtolower($taoNS[2][0]);
//                        $from = ' FROM <http://tao.ontology/'. $tao_ns .'> ';
//                }
                
                $query .= '
					SELECT ?s '.$from.' WHERE {?s ';
                
                //append conditions:
                foreach($conditions as $condition){
					$query .= ' '.$condition;
                }
                $query = substr_replace($query, '.', -2);//close conditions
                
                //add filters:
                $intersect = true;
                if(isset($options['chaining'])){
					if(strtolower($options['chaining']) == 'or'){
						$intersect = false;
					}
                }
                if($intersect){
					foreach($filters as $filter){
						$query .='
								FILTER '.$filter;
					}
                }else{
					$query .='
							FILTER (';
					$i = 0;
					foreach($filters as $filter){
						if($i>0) {
						    $query .= ' || ';
						}
						$query .= $filter;
						$i++;
					}
                }
                $query .= '}';
                
                $resultArray = $virtuoso->execQuery($query);
                $count = count($resultArray);
                for($i=0; $i<$count; $i++){
					if (isset($resultArray[$i][0])) {
						$instanceUri = $resultArray[$i][0];
						$returnValue[$instanceUri] = new core_kernel_classes_Resource($instanceUri);
					}
                }
                
                //Check in the subClasses recurslively.
                // Be carefull, it can be perf consuming with large data set and subclasses
                (isset($options['recursive'])) ? $recursive = intval($options['recursive']) : $recursive = 0;
                if($recursive){
					$recursive--;
					foreach($resource->getSubClasses(true) as $subClass){
						unset($propertyFilters[RDF_TYPE]);//reset the RDF_TYPE filter for recursive searching!!!
						$returnValue = array_merge(
								$returnValue, 
								$subClass->searchInstances($propertyFilters, array_merge($options, array('recursive' => $recursive)))
						);
					}
                }
        }
        
        
        // section 10-13-1--128--26678bb4:12fbafcb344:-8000:00000000000014F0 end

        return (array) $returnValue;
    }

    /**
     * Short description of method countInstances
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  array propertyFilters
     * @param  array options
     * @return Integer
     */
    public function countInstances( core_kernel_classes_Resource $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = null;

        // section 127-0-1-1--700ce06c:130dbc6fc61:-8000:000000000000159D begin
        
        list($NS, $ID) = explode('#', $resource->getUri());
        if(isset($ID) && !empty($ID)){
                
                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                
                $query = 'PREFIX resourceNS: <'.$NS.'#>  SELECT ?s WHERE {?s rdf:type resourceNS:'.$ID.'}';
                $resultArray = $virtuoso->execQuery($query);
                $returnValue = count($resultArray);
        }
        
        // section 127-0-1-1--700ce06c:130dbc6fc61:-8000:000000000000159D end

        return $returnValue;
    }

    /**
     * Short description of method getInstancesPropertyValues
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function getInstancesPropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        // section 127-0-1-1--120bf54f:13142fdf597:-8000:000000000000312D begin
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--120bf54f:13142fdf597:-8000:000000000000312D end

        return (array) $returnValue;
    }

    /**
     * Short description of method unsetProperty
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return boolean
     */
    public function unsetProperty( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4f08ff91:131764e4b1f:-8000:00000000000031F8 begin
        
		$domain = new core_kernel_classes_Property(RDFS_DOMAIN,__METHOD__);
		$instanceProperty = new core_kernel_classes_Resource($property->getUri(),__METHOD__);
		$returnValue = $instanceProperty->removePropertyValues($domain, array('pattern' => $resource->getUri()));
		
        // section 127-0-1-1-4f08ff91:131764e4b1f:-8000:00000000000031F8 end

        return (bool) $returnValue;
    }

    /**
     * Should not be called by application code, please use
     * core_kernel_classes_ResourceFactory::create() 
     * or core_kernel_classes_Class::createInstanceWithProperties()
     * instead
     *
     * Creates a new instance using the properties provided.
     * May NOT contain additional types in the properties array
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class type
     * @param  array properties
     * @return core_kernel_classes_Resource
     * @see core_kernel_classes_ResourceFactory
     */
    public function createInstanceWithProperties( core_kernel_classes_Class $type, $properties)
    {
        $returnValue = null;

        // section 127-0-1-1--49b11f4f:135c41c62e3:-8000:0000000000001947 begin
        if (isset($properties[RDF_TYPE])) {
        	throw new core_kernel_persistence_Exception('Additional types in createInstanceWithProperties not permited');
        }
        
        $properties[RDF_TYPE] = $type;
		$returnValue = new core_kernel_classes_Resource(common_Utils::getNewUri(), __METHOD__);
		$returnValue->setPropertiesValues($properties);
        // section 127-0-1-1--49b11f4f:135c41c62e3:-8000:0000000000001947 end

        return $returnValue;
    }

    /**
     * Delete a collection of instances of the Class.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource The resource (class) on which to apply the deletion.
     * @param  array resources An array containing core_kernel_classes_Resource objects or URIs.
     * @param  boolean deleteReference If set to true, references to instances will be deleted accross the database.
     * @return boolean
     */
    public function deleteInstances( core_kernel_classes_Resource $resource, $resources, $deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85-46895b07:13b99a96e9b:-8000:0000000000001DF5 begin
        throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 10-13-1-85-46895b07:13b99a96e9b:-8000:0000000000001DF5 end

        return (bool) $returnValue;
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

        // section 10-13-1-85--2c835591:13bffd6ae29:-8000:0000000000001E78 begin
        throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 10-13-1-85--2c835591:13bffd6ae29:-8000:0000000000001E78 end

        return (bool) $returnValue;
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

        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022ED begin
        
        if (core_kernel_persistence_virtuoso_Class::$instance == null){
        	core_kernel_persistence_virtuoso_Class::$instance = new core_kernel_persistence_virtuoso_Class();
        }
        $returnValue = core_kernel_persistence_virtuoso_Class::$instance;
        
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022ED end

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

        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022EF begin
        
        list($NS, $id) = explode('#', $resource->getUri());
        if(isset($id) && !empty($id)){
                
                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                
                $query = 'PREFIX resourceNS: <'.$NS.'#>
                        SELECT ?p ?o WHERE {resourceNS:'.$id.' ?p ?o} LIMIT 1';
                
                $resultArray = $virtuoso->execQuery($query);
                $returnValue = count($resultArray)?true:false;
        }
        
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022EF end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_virtuoso_Class */

?>