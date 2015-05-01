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
 *               2015 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

use oat\generis\model\data\ModelManager;

/**
 * Resource implements rdf:resource container identified by an uri (a string).
 * Methods enable meta data management for this resource
 *
 * @access public
 * @author patrick.plichart@tudor.lu
 * @package generis
 * @see http://www.w3.org/RDF/
 
 * @version v1.0
 */
class core_kernel_classes_Resource
    extends core_kernel_classes_Container
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * long uri as string (including namespace)
     * direct access to the uri is deprecated,
     * please use getUri()
     *
     * @access public
     * @var string
     * @deprecated
     */
    public $uriResource = '';

    /**
     * The resource label
     * direct access to the label is deprecated,
     * please use getLabel() 
     *
     * @access public
     * @var string
     * @deprecated
     */
    public $label = null;

    /**
     * The resource comment
     * direct access to the comment is deprecated,
     * please use getComment() 
     *
     * @access public
     * @var string
     * @deprecated
     */
    public $comment = '';

    // --- OPERATIONS ---
    /**
     * 
     * @return core_kernel_persistence_ResourceInterface
     */
    private function getImplementation() {
        return ModelManager::getModel()->getRdfsInterface()->getResourceImplementation();
    }
    

    /**
     * create the object
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  mixed uri
     * @param  string debug
     * @return void
     */
    public function __construct($uri, $debug = '')
    {
        
        //we should check using utils if the uri is short or long always use long uri inside the api (nevertheless the api may be called with short )
        if(!is_string($uri)){
			
			if($uri instanceof self){
				$uri = $uri->getUri();
			} else {
				$trace=debug_backtrace();
				$caller=array_shift($trace);

				throw new common_exception_Error('could not create resource from ' . (is_object($uri) ? get_class($uri) : gettype($uri)).' debug: '.$debug);
			}
		}else if(empty($uri)){
		    
			throw new common_exception_Error('cannot construct the resource because the uri cannot be empty, debug: '.$debug);
		}
		
		$this->uriResource = $uri;
        
        if(DEBUG_MODE){
        	$this->debug = $debug;
        }
        
    }


    /**
     * Conveniance method to duplicate a resource using the clone keyword
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function __clone()
    {
        throw new common_exception_DeprecatedApiMethod('Use duplicated instead, because clone resource could not share same uri that original');
    }

    /**
     * returns true if the resource is a valid class (using facts or entailment
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @return boolean
     * @see http://www.w3.org/RDF/
     */
    public function isClass()
    {
        $returnValue = (bool) false;
        if (count($this->getPropertyValues(new core_kernel_classes_Property(RDFS_SUBCLASSOF))) > 0) {
        	$returnValue = true;
        } else {
	        foreach($this->getTypes() as $type){
	        	if($type->getUri() == RDFS_CLASS){
	        		$returnValue = true;
	        		break;
	        	}
	        }
        }
        return (bool) $returnValue;
    }

    /**
     * returns true if the resource is a valid property (using facts or
     * rules)
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @return boolean
     * @see http://www.w3.org/RDF/
     */
    public function isProperty()
    {
        $returnValue = (bool) false;
        foreach($this->getTypes() as $type){
        	if($type->getUri() == RDF_PROPERTY){
        		$returnValue = true;
        		break;
        	}
        }
        return (bool) $returnValue;
    }

    /**
     * Returns all the types of this resource as core_kernel_classes_Class objects.
     *
     * @author Joel Bout <joel@taotesting.com>
     * @return core_kernel_classes_Class[] An associative array where keys are class URIs and values are core_kernel_classes_Class objects.
     */
    public function getTypes()
    {
        return $this->getImplementation()->getTypes($this);
    }

    /**
     * Returns the label of this resource as a string. This method is a convenience
     * method preventing to call the get getPropertyValues() method for a such common
     * operation. 
     * 
     * @author Patrick Plichart <patrick@taotesting.com>
     * @return string A Uniform Resource Identifier (URI).
     */
    public function getLabel()
    {
        if (is_null($this->label)) {
            
            $label =  $this->getOnePropertyValue(new core_kernel_classes_Property(RDFS_LABEL));
            $this->label = ((is_null($label) === false) ? $label->literal : '');
        }
        
        return $this->label;
    }

    /**
     * alias to setPropertyValue using rdfs:label property
     *
     * @access public
     * @author patrick.plichart@tudor:lu
     * @param  string label
     * @return boolean
     */
    public function setLabel($label)
    {
        $returnValue = (bool) false;
        $this->removePropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
        $this->setPropertyValue(new core_kernel_classes_Property(RDFS_LABEL), $label);
        $this->label = $label;
        return (bool) $returnValue;
    }

    /**
     * Short description of method getComment
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getComment()
    {
        $returnValue = (string) '';
        if($this->comment == '') {
            $comment =  $this->getOnePropertyValue(new core_kernel_classes_Property(RDFS_COMMENT));
            $this->comment = $comment != null ? $comment->literal : '';
             
        }
        $returnValue = $this->comment;
        return (string) $returnValue;
    }

    /**
     * alias to setPropertyValue using rdfs:label property
     *
     * @access public
     * @author patrick.plichart
     * @param  string comment
     * @return boolean
     */
    public function setComment($comment)
    {
        $returnValue = (bool) false;
        $this->removePropertyValues(new core_kernel_classes_Property(RDFS_COMMENT));
        $this->setPropertyValue(new core_kernel_classes_Property(RDFS_COMMENT), $comment);
        $this->comment = $comment;
        return (bool) $returnValue;
    }

    /**
     * Returns a collection of triples with all objects found for the provided
     * regarding the contextual resource.
     * The return format is an array of strings
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property uriProperty is string and may be short in the case of a locally defined property (module namespace), or long uri
     * @param  array options
     * @return array
     */
    public function getPropertyValues( core_kernel_classes_Property $property, $options = array())
    {
        $returnValue = array();
        $returnValue = $this->getImplementation()->getPropertyValues($this, $property, $options);
        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValuesCollection
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesCollection( core_kernel_classes_Property $property)
    {
        $returnValue = new core_kernel_classes_ContainerCollection($this);
		foreach ($this->getPropertyValues($property) as $value){
			$returnValue->add(common_Utils::toResource($value));
		}
        return $returnValue;
    }

    /**
     * Short description of method getUniquePropertyValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @throws common_Exception
     * @throws core_kernel_classes_EmptyProperty
     * @return core_kernel_classes_Container
     */
    public function getUniquePropertyValue( core_kernel_classes_Property $property)
    {
        $returnValue = null;

        $collection = $this->getPropertyValuesCollection($property);

        if($collection->isEmpty()){
        	throw new core_kernel_classes_EmptyProperty($this, $property);
        }
        if($collection->count() == 1 ) {
            $returnValue= $collection->get(0);
        }
        else {
            throw new core_kernel_classes_MultiplePropertyValuesException($this,$property);
        }
        return $returnValue;
    }

    /**
     * Helper to return one property value, since there is no order
     * if there are multiple values the value to be returned will be choosen by random
     * 
     * optional parameter $last should NOT be used since it is no longer supported
     * and will be removed in future versions
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  boolean last
     * @return core_kernel_classes_Container
     */
    public function getOnePropertyValue( core_kernel_classes_Property $property, $last = false)
    {
        $returnValue = null;
        if($last){
            throw new core_kernel_persistence_Exception('parameter \'last\' for getOnePropertyValue no longer supported');
        };
        
		$options = array(
			'forceDefaultLg' => true,
		    'one' => true
		);  

		$value = $this->getPropertyValues($property, $options);
		
		if (count($value)){
			$returnValue = common_Utils::toResource(current($value));
		}

        return $returnValue;
    }

    /**
     * Short description of method getPropertyValuesByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  string lg
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesByLg( core_kernel_classes_Property $property, $lg)
    {
        $returnValue = null;
        $returnValue = $this->getImplementation()->getPropertyValuesByLg($this, $property, $lg);
        return $returnValue;
    }

    /**
     * assign the (string) object for the provided uriProperty reagarding the
     * resource
     *
     * @access public
     * @author Patrick.plichart
     * @param  Property property
     * @param  string object
     * @return boolean
     * @version 1.0
     */
    public function setPropertyValue( core_kernel_classes_Property $property, $object)
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->setPropertyValue($this, $property, $object);
        return (bool) $returnValue;
    }

    /**
     * Set multiple properties and their value at one time. 
     * Conveniance method isntead of adding the property values one by one.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array propertiesValues
     * @return boolean
     */
    public function setPropertiesValues($propertiesValues)
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->setPropertiesValues($this, $propertiesValues);
        return (bool) $returnValue;
    }

    /**
     * Short description of method setPropertyValueByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  string value
     * @param  string lg
     * @return boolean
     */
    public function setPropertyValueByLg( core_kernel_classes_Property $property, $value, $lg)
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->setPropertyValueByLg($this, $property, $value, $lg);
        return (bool) $returnValue;
    }

    /**
     * edit the assigned value(s) for the provided uriProperty regarding the
     * resource using the provided object. Specific assignation edition (a
     * triple) shouldbe made using triple management
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  string object
     * @return boolean
     */
    public function editPropertyValues( core_kernel_classes_Property $property, $object)
    {
        $returnValue =  $this->removePropertyValues($property);
        if(is_array($object)){
            foreach($object as $value){
                $returnValue = $this->setPropertyValue($property, $value);
            }
        }else{
            $returnValue = $this->setPropertyValue($property, $object);
        }

        return (bool) $returnValue;
    }

    /**
     * Short description of method editPropertyValueByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property prop
     * @param  string value
     * @param  string lg
     * @return boolean
     */
    public function editPropertyValueByLg( core_kernel_classes_Property $prop, $value, $lg)
    {
        $returnValue = (bool) false;   
        $returnValue = $this->removePropertyValueByLg($prop, $lg);
        $returnValue &= $this->setPropertyValueByLg($prop, $value, $lg);
        return (bool) $returnValue;
    }
    
    /**
     * remove a single triple with this subject and predicate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  mixed value
     * @return boolean
     */
    public function removePropertyValue( core_kernel_classes_Property $property, $value)
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->removePropertyValues($this, $property, array(
        	'pattern'	=> (is_object($value) && $value instanceof self ? $value->getUri() : $value),
        	'like'		=> false 
        ));      
        return (bool) $returnValue;
    }    

    /**
     * remove all triples with this subject and predicate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  array options
     * @return boolean
     */
    public function removePropertyValues( core_kernel_classes_Property $property, $options = array())
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->removePropertyValues($this, $property, $options);
        return (bool) $returnValue;
    }

    /**
     * Short description of method removePropertyValueByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property prop
     * @param  string lg
     * @param  array options
     * @return boolean
     */
    public function removePropertyValueByLg( core_kernel_classes_Property $prop, $lg, $options = array())
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->removePropertyValueByLg($this, $prop, $lg, $options);
        return (bool) $returnValue;
    }

    /**
     * returns all generis statements about an uri, rdf level (there is no
     * inferred by the rdfs level), restricted according to rights priviliges
     * on statements
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRdfTriples()
    {
        $returnValue = null;
        $returnValue = $this->getImplementation()->getRdfTriples($this);
        return $returnValue;
    }

    /**
     * return the languages in which a value exists for uriProperty for this
     *
     * @access public
     * @author aptrick.plichart@tudor.lu
     * @param  Property property
     * @return array
     */
    public function getUsedLanguages( core_kernel_classes_Property $property)
    {
        $returnValue = array();
        $returnValue = $this->getImplementation()->getUsedLanguages($this, $property);
        return (array) $returnValue;
    }

    /**
     * Duplicate a resource: create a new URI and duplicate all the triples
     * those with the predicate listed in excludedProperties.
     * The method returns the new resource.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array excludedProperties
     * @return core_kernel_classes_Resource
     */
    public function duplicate($excludedProperties = array())
    {
        $returnValue = null;
        $returnValue = $this->getImplementation()->duplicate($this, $excludedProperties);
        return $returnValue;
    }

    /**
     * remove any assignation made to this resource, the uri is consequently
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean deleteReference set deleteRefence to true when you need that all reference to this resource are removed.
     * @return boolean
     */
    public function delete($deleteReference = false)
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->delete($this, $deleteReference);
        return (bool) $returnValue;
    }



    /**
     * Short description of method __toString
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function __toString()
    {
        return $this->getUri()."\n" . $this->getLabel() ;
    }

    /**
     * Short description of method getPropertiesValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array properties
     * @return array
     */
    public function getPropertiesValues($properties)
    {
        $returnValue = array();
        if(!is_array($properties)){
			throw new common_exception_InvalidArgumentType(__CLASS__, __FUNCTION__, 0, 'array', $properties);
        }
        $returnValue = $this->getImplementation()->getPropertiesValues($this, $properties/*, $last*/);
        return (array) $returnValue;
    }

    /**
     * Short description of method setType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class type
     * @return boolean
     */
    public function setType( core_kernel_classes_Class $type)
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->setType($this, $type);
        return (bool) $returnValue;
    }

    /**
     * Short description of method removeType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class type
     * @return boolean
     */
    public function removeType( core_kernel_classes_Class $type)
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->removeType($this, $type);
        return (bool) $returnValue;
    }

    /**
     * Short description of method hasType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function hasType( core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;
    	foreach($this->getTypes() as $type){
        	if ($class->equals($type)){
        		$returnValue = true;
        		break;
        	}
        }
        return (bool) $returnValue;
    }

    /**
     * Short description of method exists
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function exists()
    {
        $returnValue = (bool) false;
        try{
        	$returnValue = count($this->getTypes())?true:false;
        }
        catch(Exception $e){
        	;//return false by default
        }
        return (bool) $returnValue;
    }

    /**
     * returns the full URI as string (including namespace)
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getUri()
    {
        $returnValue = (string) '';
        $returnValue = $this->uriResource;
        return (string) $returnValue;
    }

    /**
     * Short description of method equals
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function equals( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;
        $returnValue = $this->getUri() == $resource->getUri();
        return (bool) $returnValue;
    }

    /**
     * Whenever or not the current resource is
     * an instance of the specified class
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function isInstanceOf( core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;
        foreach($this->getTypes() as $type){
        	if ($class->equals($type) || $type->isSubClassOf($class)){
        		$returnValue = true;
        		break;
        	}
        }
        return (bool) $returnValue;
    }
  
}