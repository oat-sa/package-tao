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
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage models_classes
 */


/**
 * Service is the base class of all services, and implements the singleton
 * for derived services
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage models_classes
 */
abstract class tao_models_classes_GenerisService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * constructor
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    protected function __construct()
    {
        // section 10-13-1-45-792423e0:12398d13f24:-8000:000000000000183D begin
        // section 10-13-1-45-792423e0:12398d13f24:-8000:000000000000183D end
    }

    /**
     * 
     * @access public
     * @author patrick
     * @param  core_kernel_classes_Resource $resource
     * @return core_kernel_classes_Resource with all properties - values
     */
    public function getResourceDescription( core_kernel_classes_Resource $resource){
	return $resource->getResourceDescription(true);

    }

    /**
     * search the instances matching the filters in parameters
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array propertyFilters
     * @param  Class topClazz
     * @param  array options
     * @return array
     */
    public function searchInstances($propertyFilters = array(),  core_kernel_classes_Class $topClazz = null, $options = array())
    {
        $returnValue = array();

        // section 127-0-1-1-106f2734:126b2f503d0:-8000:0000000000001E96 begin

        if(!is_null($topClazz)){
        	$returnValue = $topClazz->searchInstances($propertyFilters, $options);
        }


        // section 127-0-1-1-106f2734:126b2f503d0:-8000:0000000000001E96 end

        return (array) $returnValue;
    }

    /**
     * Get the class of the resource in parameter (the rdfs type property)
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource instance
     * @return core_kernel_classes_Class
     */
    public function getClass( core_kernel_classes_Resource $instance)
    {
        $returnValue = null;

        // section 127-0-1-1--519643a:127850ba1cf:-8000:000000000000233B begin

     	if(!is_null($instance)){
        	if(!$instance->isClass() && !$instance->isProperty()){
        		foreach($instance->getTypes() as $type){
        			$returnValue = $type;
        			break;
        		}
        	}
        }

        // section 127-0-1-1--519643a:127850ba1cf:-8000:000000000000233B end

        return $returnValue;
    }

    /**
     * Instantiate an RDFs Class
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @return core_kernel_classes_Resource
     */
    public function createInstance( core_kernel_classes_Class $clazz, $label = '')
    {
        $returnValue = null;

        // section 10-13-1-45--135fece8:123b76cb3ff:-8000:0000000000001897 begin

        if( empty($label) ){
			$label =  $this->createUniqueLabel($clazz);
		}

		$returnValue = core_kernel_classes_ResourceFactory::create($clazz, $label, '');

        // section 10-13-1-45--135fece8:123b76cb3ff:-8000:0000000000001897 end

        return $returnValue;
    }

    /**
     * Short description of method createUniqueLabel
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class clazz
     * @param  boolean subClassing
     * @return string
     */
    public function createUniqueLabel( core_kernel_classes_Class $clazz, $subClassing = false)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5449e54e:12a6a9d50dc:-8000:0000000000002487 begin


        if($subClassing){
        	$labelBase = $clazz->getLabel() . '_' ;
        	$count = count($clazz->getSubClasses()) +1;
        }
        else{
        	$labelBase = $clazz->getLabel() . ' ' ;
        	$count = count($clazz->getInstances()) +1;
        }

		$options = array(
			'lang' 				=> core_kernel_classes_Session::singleton()->getDataLanguage(),
			'like' 				=> false,
			'recursive'  		=> false
		);

		do{
			$exist = false;
			$label =  $labelBase . $count;
			$result = $clazz->searchInstances(array(RDFS_LABEL => $label), $options);
			if(count($result) > 0){
				$exist = true;
				$count ++;
			}
		} while($exist);

		$returnValue = $label;

        // section 127-0-1-1-5449e54e:12a6a9d50dc:-8000:0000000000002487 end

        return (string) $returnValue;
    }

    /**
     * Subclass an RDFS Class
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class parentClazz
     * @param  string label
     * @return core_kernel_classes_Class
     */
    public function createSubClass( core_kernel_classes_Class $parentClazz, $label = '')
    {
        $returnValue = null;

        // section 127-0-1-1-404a280c:12475f095ee:-8000:0000000000001AB5 begin

        if( empty($label) ){
			$label = $this->createUniqueLabel($parentClazz, true);
		}
		$returnValue = $parentClazz->createSubClass($label, '');

        // section 127-0-1-1-404a280c:12475f095ee:-8000:0000000000001AB5 end

        return $returnValue;
    }

    /**
     * bind the given RDFS properties to the RDFS resource in parameter
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource instance
     * @param  array properties
     * @return core_kernel_classes_Resource
     */
    public function bindProperties( core_kernel_classes_Resource $instance, $properties = array())
    {
        $returnValue = null;

        // section 10-13-1-45--135fece8:123b76cb3ff:-8000:00000000000018A5 begin
        $binder = new tao_models_classes_dataBinding_GenerisInstanceDataBinder($instance);
        $binder->bind($properties);

        $returnValue = $instance;

        // section 10-13-1-45--135fece8:123b76cb3ff:-8000:00000000000018A5 end

        return $returnValue;
    }

    /**
     * duplicate a resource
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource instance
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneInstance( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 127-0-1-1-50de96c6:1266ae198e7:-8000:0000000000001E30 begin
        if (is_null($clazz)) {
            $types = $instance->getTypes();
            $clazz = current($types);
        }

   		$returnValue = $this->createInstance($clazz);
		if(!is_null($returnValue)){
			$properties = $clazz->getProperties(true);
			foreach($properties as $property){
			    $this->cloneInstanceProperty($instance, $returnValue, $property);
			}
			$label = $instance->getLabel();
			$cloneLabel = "$label bis";
			if(preg_match("/bis/", $label)){
				$cloneNumber = (int)preg_replace("/^(.?)*bis/", "", $label);
				$cloneNumber++;
				$cloneLabel = preg_replace("/bis(.?)*$/", "", $label)."bis $cloneNumber" ;
			}

			$returnValue->setLabel($cloneLabel);
		}

        // section 127-0-1-1-50de96c6:1266ae198e7:-8000:0000000000001E30 end

        return $returnValue;
    }
    
    protected function cloneInstanceProperty( core_kernel_classes_Resource $source, core_kernel_classes_Resource $destination, core_kernel_classes_Property $property) {
        $range = $property->getRange();
		// Avoid doublons, the RDF TYPE property will be set by the implementation layer
        if ($property->getUri() != RDF_TYPE){
            foreach($source->getPropertyValuesCollection($property)->getIterator() as $propertyValue){
                if(!is_null($range) && $range->getUri() == CLASS_GENERIS_FILE){
                    $file = new core_kernel_versioning_File($propertyValue->getUri());
                    $newFile = $file->getRepository()->spawnFile($file->getAbsolutePath(), $file->getLabel());
                    $destination->setPropertyValue($property, $newFile);
                } else {
                    $destination->setPropertyValue($property, $propertyValue);
                }
            }
        }
    }
    

    /**
     * Clone a Class and move it under the newParentClazz
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class sourceClazz
     * @param  Class newParentClazz
     * @param  Class topLevelClazz
     * @return core_kernel_classes_Class
     */
    public function cloneClazz( core_kernel_classes_Class $sourceClazz,  core_kernel_classes_Class $newParentClazz = null,  core_kernel_classes_Class $topLevelClazz = null)
    {
        $returnValue = null;

        // section 127-0-1-1-6c3e90c1:1288272e8b7:-8000:0000000000001F3F begin

    	if(!is_null($sourceClazz) && !is_null($newParentClazz)){
        	if((is_null($topLevelClazz))){
        		$properties = $sourceClazz->getProperties(false);
        	}
        	else{
        		$properties = $this->getClazzProperties($sourceClazz, $topLevelClazz);
        	}

        	//check for duplicated properties
        	$newParentProperties = $newParentClazz->getProperties(true);
        	foreach($properties as $index => $property){
        		foreach($newParentProperties as $newParentProperty){
        			if($property->getUri() == $newParentProperty->getUri()){
        				unset($properties[$index]);
        				break;
        			}
        		}
        	}

        	//create a new class
        	$returnValue = $this->createSubClass($newParentClazz, $sourceClazz->getLabel());

        	//assign the properties of the source class
        	foreach($properties as $property){
        		$property->setDomain($returnValue);
        	}
        }

        // section 127-0-1-1-6c3e90c1:1288272e8b7:-8000:0000000000001F3F end

        return $returnValue;
    }

    /**
     * Change the Class (RDFS_TYPE) of a resource
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource instance
     * @param  Class destinationClass
     * @return boolean
     */
    public function changeClass( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $destinationClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--4b0a5ad3:12776b15903:-8000:0000000000002331 begin

   		try{
        	foreach($instance->getTypes() as $type){
        		$instance->removeType($type);
        	}
        	$instance->setType($destinationClass);
        	foreach($instance->getTypes() as $type){
        		if($type->getUri() == $destinationClass->getUri()){
        			$returnValue = true;
        			break;
        		}
        	}
        }
        catch(common_Exception $ce){
        	print $ce;
        }

        // section 127-0-1-1--4b0a5ad3:12776b15903:-8000:0000000000002331 end

        return (bool) $returnValue;
    }

    /**
     * Get all the properties of the class in parameter.
     * The properties are taken recursivly into the class parents up to the top
     * class.
     * If the top level class is not defined, we used the TAOObject class.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class clazz
     * @param  Class topLevelClazz
     * @return array
     */
    public function getClazzProperties( core_kernel_classes_Class $clazz,  core_kernel_classes_Class $topLevelClazz = null)
    {
        $returnValue = array();

        // section 127-0-1-1--250780b8:12843f3062f:-8000:0000000000002405 begin

        if(is_null($topLevelClazz)){
			$topLevelClazz = new core_kernel_classes_Class(TAO_OBJECT_CLASS);
		}

		if($clazz->getUri() == $topLevelClazz->getUri()){
			$returnValue = $clazz->getProperties(false);
			return (array) $returnValue;
		}

		//determine the parent path
		$parents = array();
		$top = false;
		do{
			if(!isset($lastLevelParents)){
				$parentClasses = $clazz->getParentClasses(false);
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
				if($parentClass->getUri() == $topLevelClazz->getUri() ) {
					$parents[$parentClass->getUri()] = $parentClass;
					$top = true;
					break;
				}
				if($parentClass->getUri() == RDFS_CLASS){
					continue;
				}

				$allParentClasses = $parentClass->getParentClasses(true);
				if(array_key_exists($topLevelClazz->getUri(), $allParentClasses)){
					 $parents[$parentClass->getUri()] = $parentClass;
				}
				$lastLevelParents[$parentClass->getUri()] = $parentClass;
			}
		}while(!$top);

		foreach($parents as $parent){
			$returnValue = array_merge($returnValue, $parent->getProperties(false));
    	}

		$returnValue = array_merge($returnValue, $clazz->getProperties(false));

        // section 127-0-1-1--250780b8:12843f3062f:-8000:0000000000002405 end

        return (array) $returnValue;
    }

    /**
     * get the properties of the source class that are not in the destination
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class sourceClass
     * @param  Class destinationClass
     * @return array
     */
    public function getPropertyDiff( core_kernel_classes_Class $sourceClass,  core_kernel_classes_Class $destinationClass)
    {
        $returnValue = array();

        // section 127-0-1-1--4b0a5ad3:12776b15903:-8000:0000000000002337 begin

    	$sourceProperties = $sourceClass->getProperties(true);
        $destinationProperties = $destinationClass->getProperties(true);

        foreach($sourceProperties as $sourcePropertyUri => $sourceProperty){
        	if(!array_key_exists($sourcePropertyUri, $destinationProperties)){
        		array_push($returnValue, $sourceProperty);
        	}
        }

        // section 127-0-1-1--4b0a5ad3:12776b15903:-8000:0000000000002337 end

        return (array) $returnValue;
    }

    /**
     * get the properties of an instance for a specific language
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource instance
     * @param  string lang
     * @return array
     */
    public function getTranslatedProperties( core_kernel_classes_Resource $instance, $lang)
    {
        $returnValue = array();

        // section 127-0-1-1--1254e308:126aced7510:-8000:0000000000001E84 begin

    	try{
			foreach($instance->getTypes() as $clazz){
				foreach($clazz->getProperties(true) as $property){

					if($property->isLgDependent() || $property->getUri() == RDFS_LABEL){
						$collection = $instance->getPropertyValuesByLg($property, $lang);
						if($collection->count() > 0 ){

							if($collection->count() == 1){
								$returnValue[$property->getUri()] = (string)$collection->get(0);
							}
							else{
								$propData = array();
								foreach($collection->getIterator() as $collectionItem){
									$propData[] = (string)$collectionItem;
								}
								$returnValue[$property->getUri()] = $propData;
							}
						}
					}
				}
			}
		}
		catch(Exception $e){
			print $e;
		}

        // section 127-0-1-1--1254e308:126aced7510:-8000:0000000000001E84 end

        return (array) $returnValue;
    }

    /**
     * Format an RDFS Class to an array
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class clazz
     * @return array
     */
    public function toArray( core_kernel_classes_Class $clazz)
    {
        $returnValue = array();

        // section 127-0-1-1-1f98225a:12544a8e3a3:-8000:0000000000001C80 begin

    	$properties = $clazz->getProperties(false);
		foreach($clazz->getInstances(false) as $instance){
			$data = array();
			foreach($properties	as $property){

				$data[$property->getLabel()] = null;

				$values = $instance->getPropertyValues($property);
				if(count($values) > 1){
					$data[$property->getLabel()] = $values;
				}
				elseif(count($values) == 1){
					$data[$property->getLabel()] = $values[0];
				}
			}
			array_push($returnValue, $data);
		}

        // section 127-0-1-1-1f98225a:12544a8e3a3:-8000:0000000000001C80 end

        return (array) $returnValue;
    }

    /**
     * Format an RDFS Class to an array to be interpreted by the client tree
     * This is a closed array format.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class clazz
     * @param  array options
     * @return array
     */
    public function toTree( core_kernel_classes_Class $clazz, $options)
    {
        $returnValue = array();

        // section 127-0-1-1-404a280c:12475f095ee:-8000:0000000000001A9B begin
        // show subclasses yes/no, not implemented
		$subclasses = (isset($options['subclasses'])) ? $options['subclasses'] : true;
		// show instances yes/no
		$instances = (isset($options['instances'])) ? $options['instances'] : true;
		// @todo describe how this option influences the behaviour
		$highlightUri = (isset($options['highlightUri'])) ? $options['highlightUri'] : '';
		// filter results by label, and don't show them as a tree at all, but a flat list
		$labelFilter = (isset($options['labelFilter'])) ? $options['labelFilter'] : '';
		// @todo describe how this option influences the behaviour
		$recursive = (isset($options['recursive'])) ? $options['recursive'] : false;
		// cut of the class and only display the children?
		$chunk = (isset($options['chunk'])) ? $options['chunk'] : false;
		// probably which subtrees should be opened
		$browse = (isset($options['browse'])) ? $options['browse'] : array();
		// limit of instances shown by subclass if no search label is given
		// if a search string is given, this is the total limit of results, independant of classes
		$limit = (isset($options['limit'])) ? $options['limit'] : 0;
		// offset for limit
		$offset = (isset($options['offset'])) ? $options['offset'] : 0;
                //an array used to filter properties; use the format by core_kernel_classes_Class::searchInstances
		$propertyFilter = (isset($options['propertyFilter'])) ? $options['propertyFilter'] : array();
                
                
		$factory = new tao_models_classes_GenerisTreeFactory();
		if (!empty($labelFilter) && $labelFilter!='*') {
			$props	= array(RDFS_LABEL => $labelFilter);
			$opts	= array(
				'like'		=> true,
				'limit'		=> $limit,
				'offset'	=> $offset,
				'recursive'	=> true
			); 
			$searchResult = $clazz->searchInstances($props, $opts);
			$results = array();
			foreach ($searchResult as $instance){
				$results[] = $factory->buildResourceNode($instance);
			}
			if ($offset > 0) {
				$returnValue = $results;
			} else {
				$returnValue = $factory->buildClassNode($clazz);
				$returnValue['count']		= $clazz->countInstances($props, $opts);;
				$returnValue['children']	= $results;
			}
		} else {
			array_walk($browse, function(&$item, $key) {
				$item = tao_helpers_Uri::decode($item);
			});
			
			$browse[] = $clazz->getUri();
			$tree = $factory->buildTree($clazz, $instances, $browse, $limit, $offset, $propertyFilter);
			$returnValue = $chunk ? ($tree['children']) : $tree;
		}
		return $returnValue;
    }
    
} /* end of abstract class tao_models_classes_GenerisService */

?>