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
 * Resource implements rdf:resource container identified by an uri (a string).
 * Methods enable meta data management for this resource
 *
 * @access public
 * @author patrick.plichart@tudor.lu
 * @package core
 * @see http://www.w3.org/RDF/
 * @subpackage kernel_classes
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
    public $label = '';

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
        // section 127-0-0-1-59fa2263:1193cca7051:-8000:0000000000000AFB begin
        //we should check using utils if the uri is short or long always use long uri inside the api (nevertheless the api may be called with short )
        if(!is_string($uri)){
			//var_dump(debug_backtrace());
			if($uri instanceof self){
				$uri = $uri->getUri();
			} else {
				throw new common_exception_Error('cannot construct the resource because the uri is not a "string", but a '.gettype($uri).' debug: '.$debug);
			}
		}else if(empty($uri)){
			throw new common_exception_Error('cannot construct the resource because the uri cannot be empty, debug: '.$debug);
		}
		
		$this->uriResource = $uri;
        
        if(DEBUG_MODE){
        	$this->debug = $debug;
        }
        // section 127-0-0-1-59fa2263:1193cca7051:-8000:0000000000000AFB end
    }


    /**
     * returns all properties values describing the resource 
     * @param fromDefinition specify if the properties should be computed from resources types (slow) or from effective values
     * @return object {uri, properties}
     */
    public function getResourceDescription($fromDefinition = true){
        $returnValue = null;
        $properties =array();
        if ($fromDefinition){
            $types = $this->getTypes();
            foreach ($types as $type){
                foreach ($type->getProperties(true) as $property){
                    //$this->$$property->getUri() = array($property->getLabel(),$this->getPropertyValues());
                    $properties[$property->getUri()] = $property;
                }
            }
            $properties = array_unique($properties);
            $propertiesValues =  $this->getPropertiesValues($properties);
            if (count($propertiesValues)==0) {
                throw new common_exception_NoContent();
            }
            $propertiesValuesStdClasses = self::propertiesValuestoStdClasses($propertiesValues);
        }
        else	//get effective triples and map the returned information into the same structure
        {
            $triples = $this->getRdfTriples();
            if (count( $triples)==0) {
                throw new common_exception_NoContent();
            }
            foreach ($triples as $triple){
                $properties[$triple->predicate][] = common_Utils::isUri($triple->object)
                ? new core_kernel_classes_Resource($triple->object)
                : new core_kernel_classes_Literal($triple->object);
            }
            $propertiesValuesStdClasses = self::propertiesValuestoStdClasses($properties);
        }
        $resource = new stdClass;
        $resource->uri = $this->getUri();
        $resource->properties = $propertiesValuesStdClasses;
        return $resource;
    }

    /**
     * small helper (shall it be moved) more convenient data structure for propertiesValues for exchange
     * @return array
     */
    private static function propertiesValuestoStdClasses($propertiesValues = null){
        $returnValue =array();
        foreach ($propertiesValues as $uri => $values) {
            $propStdClass = new stdClass;
            $propStdClass->predicateUri = $uri;
            foreach ($values as $value){
                $stdValue = new stdClass;
                $stdValue->valueType = (get_class($value)=="core_kernel_classes_Literal") ? "literal" : "resource";
                $stdValue->value = (get_class($value)=="core_kernel_classes_Literal") ? $value->__toString() : $value->getUri();
                $propStdClass->values[]= $stdValue;
            }
            $returnValue[]=$propStdClass;
        }
        return $returnValue;
    }

    /**
     * Conveniance method to duplicate a resource using the clone keyword
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public function __clone()
    {
        $returnValue = null;

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000082A begin
        
        $returnValue = $this->duplicate();
        
        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000082A end

        return $returnValue;
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

        // section 10-13-1--31--647ec317:119141cd117:-8000:0000000000000913 begin
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

        // section 10-13-1--31--647ec317:119141cd117:-8000:0000000000000913 end

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

        // section 10-13-1--31--647ec317:119141cd117:-8000:0000000000000915 begin
        
        foreach($this->getTypes() as $type){
        	if($type->getUri() == RDF_PROPERTY){
        		$returnValue = true;
        		break;
        	}
        }
        
        // section 10-13-1--31--647ec317:119141cd117:-8000:0000000000000915 end

        return (bool) $returnValue;
    }

    /**
     * please use getTypes() instead
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @deprecated
     * @return array
     */
    public function getType()
    {
        $returnValue = array();

        // section 127-0-1-1-62cf85dc:12bab18dc39:-8000:000000000000135F begin
        common_Logger::d('Use of deprecated function getType() please use getTypes().', 'DEPRECATED');
        $returnValue = $this->getTypes();
        // section 127-0-1-1-62cf85dc:12bab18dc39:-8000:000000000000135F end

        return (array) $returnValue;
    }

    /**
     * Returns all the types of the ressource
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getTypes()
    {
        $returnValue = array();

        // section 127-0-1-1--7529374:136154ebbc3:-8000:0000000000001971 begin
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getTypes($this);
        // section 127-0-1-1--7529374:136154ebbc3:-8000:0000000000001971 end

        return (array) $returnValue;
    }

    /**
     * returns label of the resources as string, alias to getPropertyValues
     * rdfs:label property
     *
     * @access public
     * @author patrick.plichart
     * @return string
     * @see www.generis.lu/documentation/design#getLabel
     * @version 1.0
     */
    public function getLabel()
    {
        $returnValue = (string) '';

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000840 begin
        
        if($this->label == '') {
            $label =  $this->getOnePropertyValue(new core_kernel_classes_Property(RDFS_LABEL));
            $this->label = ($label != null) ? $label->literal : '';
        }
        $returnValue = $this->label;
        
        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000840 end

        return (string) $returnValue;
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

        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AA6 begin

        $this->removePropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
        $this->setPropertyValue(new core_kernel_classes_Property(RDFS_LABEL), $label);
        $this->label = $label;
        
        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AA6 end

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

        // section 127-0-1-1--f4ec538:12c30e15fc8:-8000:00000000000013A8 begin
        if($this->comment == '') {
            $comment =  $this->getOnePropertyValue(new core_kernel_classes_Property(RDFS_COMMENT));
            $this->comment = $comment != null ? $comment->literal : '';
             
        }
        $returnValue = $this->comment;
        // section 127-0-1-1--f4ec538:12c30e15fc8:-8000:00000000000013A8 end

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

        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AA8 begin
        
        $this->removePropertyValues(new core_kernel_classes_Property(RDFS_COMMENT));
        $this->setPropertyValue(new core_kernel_classes_Property(RDFS_COMMENT), $comment);
        $this->comment = $comment;
        
        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AA8 end

        return (bool) $returnValue;
    }

    /**
     * Returns a collection of triples with all objects found for the provided
     * regarding the contextual resource.
     * The return format is string, if you want get object (Resource/Literal)
     * should
     * use the function getAllPropertyValues
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

        // section 127-0-0-1-71ce5466:11938f47d30:-8000:0000000000000A99 begin
    	
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getPropertyValues($this, $property, $options);

        // section 127-0-0-1-71ce5466:11938f47d30:-8000:0000000000000A99 end

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
        $returnValue = null;

        // section 10-13-1--99--32cd3c54:11be55033bf:-8000:0000000000000D79 begin
    	
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getPropertyValuesCollection($this, $property);
        
        // section 10-13-1--99--32cd3c54:11be55033bf:-8000:0000000000000D79 end

        return $returnValue;
    }

    /**
     * Short description of method getUniquePropertyValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @return core_kernel_classes_Container
     */
    public function getUniquePropertyValue( core_kernel_classes_Property $property)
    {
        $returnValue = null;

        // section 10-13-1--99--2465c76a:11c0440e8db:-8000:0000000000001466 begin
 		
        $collection = $this->getPropertyValuesCollection($property);

        if($collection->isEmpty()){
        	throw new common_exception_EmptyProperty($this, $property);
        }
        if($collection->count() == 1 ) {
            $returnValue= $collection->get(0);
        }
        else {
        	$propLabel = $property->getLabel();
        	$label = $this->getLabel();
            throw new common_Exception("Property {$propLabel} ({$property->getUri()}) of resource {$label} ({$this->getUri()}) 
            							has more than one value do not use getUniquePropertyValue but use getPropertyValue instead");
        }
        	
        // section 10-13-1--99--2465c76a:11c0440e8db:-8000:0000000000001466 end

        return $returnValue;
    }

    /**
     * Short description of method getOnePropertyValue
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

        // section -87--2--3--76-51a982f1:1278aabc987:-8000:0000000000008925 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getOnePropertyValue($this, $property, $last);
      
        // section -87--2--3--76-51a982f1:1278aabc987:-8000:0000000000008925 end

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

        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:00000000000023A4 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getPropertyValuesByLg($this, $property, $lg);
        
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:00000000000023A4 end

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

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:00000000000007AC begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->setPropertyValue($this, $property, $object);
        
        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:00000000000007AC end

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

        // section 127-0-1-1-44e4845c:12f4ef0414d:-8000:0000000000001437 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->setPropertiesValues($this, $propertiesValues);
        
        // section 127-0-1-1-44e4845c:12f4ef0414d:-8000:0000000000001437 end

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

        // section -87--2--3--76-2d6cca2d:12579c74420:-8000:0000000000001831 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->setPropertyValueByLg($this, $property, $value, $lg);
        
        // section -87--2--3--76-2d6cca2d:12579c74420:-8000:0000000000001831 end

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
        $returnValue = (bool) false;

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:00000000000009D5 begin
        $this->removePropertyValues($property);
        if(is_array($object)){
            foreach($object as $value){
                $returnValue = $this->setPropertyValue($property, $value);
            }
        }else{
            $returnValue = $this->setPropertyValue($property, $object);
        }

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:00000000000009D5 end

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

        // section -87--2--3--76-18452630:1270a514a71:-8000:00000000000023F0 begin
        $returnValue = $this->removePropertyValueByLg($prop, $lg);
        $returnValue &= $this->setPropertyValueByLg($prop, $value, $lg);
        // section -87--2--3--76-18452630:1270a514a71:-8000:00000000000023F0 end

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

        // section 10-13-1--31--64270bf:11918ad765e:-8000:000000000000097C begin

        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->removePropertyValues($this, $property, array(
        	'pattern'	=> (is_object($value) && $value instanceof self ? $value->getUri() : $value),
        	'like'		=> false 
        ));
        
        // section 10-13-1--31--64270bf:11918ad765e:-8000:000000000000097C end

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

        // section 10-13-1--31--64270bf:11918ad765e:-8000:000000000000097C begin

        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->removePropertyValues($this, $property, $options);
        
        // section 10-13-1--31--64270bf:11918ad765e:-8000:000000000000097C end

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

        // section -87--2--3--76-18452630:1270a514a71:-8000:00000000000023EC begin
       
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->removePropertyValueByLg($this, $prop, $lg, $options);
        
        // section -87--2--3--76-18452630:1270a514a71:-8000:00000000000023EC end

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

        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000B29 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getRdfTriples($this);
        
        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000B29 end

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

        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000B13 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getUsedLanguages($this, $property);

        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000B13 end

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

        // section 127-0-1-1-440a1f14:12e71f49661:-8000:000000000000141E begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->duplicate($this, $excludedProperties);
        
        // section 127-0-1-1-440a1f14:12e71f49661:-8000:000000000000141E end

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

        // section 10-13-1--31-5c77d5ee:119187ec9d2:-8000:0000000000000976 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->delete($this, $deleteReference);
        
        // section 10-13-1--31-5c77d5ee:119187ec9d2:-8000:0000000000000976 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getPrivileges
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getPrivileges()
    {
        $returnValue = array();

        // section -87--2--3--76--148ee98a:12452773959:-8000:00000000000017DD begin
        $returnValue = array(
       						'u' => array('r' => true, 'w' => true),
        		 			'g' => array('r' => true, 'w' => true),
        		 			'a' => array('r' => true, 'w' => true)
        );
        // section -87--2--3--76--148ee98a:12452773959:-8000:00000000000017DD end

        return (array) $returnValue;
    }

    /**
     * Short description of method getLastModificationDate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @return doc_date
     */
    public function getLastModificationDate( core_kernel_classes_Property $property = null)
    {
        $returnValue = null;

        // section -87--2--3--76--148ee98a:12452773959:-8000:000000000000235D begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getLastModificationDate($this, $property);
        
        // section -87--2--3--76--148ee98a:12452773959:-8000:000000000000235D end

        return $returnValue;
    }

    /**
     * Short description of method getLastModificationUser
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getLastModificationUser()
    {
        $returnValue = (string) '';

        // section -87--2--3--76--148ee98a:12452773959:-8000:0000000000002361 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getLastModificationUser($this);
        
        // section -87--2--3--76--148ee98a:12452773959:-8000:0000000000002361 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toHtml
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function toHtml()
    {
        $returnValue = (string) '';

        // section 10-13-1--31--3bf74db1:119c3d777ef:-8000:0000000000000B3F begin
        $returnValue .= '<span style="postition:relative;margin:5px;display:block;align:center;border: #9c9c9c 1px solid;border-color:black;font-family:Verdana;background-color:#Ffffcc;width:32%;height:9%;">';
        $returnValue .= '<span style="display:block;height=10px;border: #9c9c9c 1px solid;border-color:black;font-weight:bold;text-align:center;background-color:#ffcc99;font-size:10;">';
        $returnValue .= ''.$this->getLabel();
        $returnValue .= '</span>';
        $returnValue .= '<span style="display:block;height=90px;font-weight:normal;font-style:italic;font-size:9;">';
        $returnValue .= ''.$this->getComment()."<br />";
        $returnValue .= '<span style="font-size:5;">'.$this->getUri().'</span>';
        $returnValue .= '</span>';

        $returnValue .= '</span>';

        // section 10-13-1--31--3bf74db1:119c3d777ef:-8000:0000000000000B3F end

        return (string) $returnValue;
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
        $returnValue = (string) '';

        // section 10-13-1--99-20ac9d48:11a723d33d6:-8000:0000000000001253 begin
        $returnValue = $this->getUri().'<br/>' . $this->getLabel() . '<br/>' ;
        // section 10-13-1--99-20ac9d48:11a723d33d6:-8000:0000000000001253 end

        return (string) $returnValue;
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

        // section 127-0-1-1-77557f59:12fa87873f4:-8000:00000000000014CD begin
        
        if(!is_array($properties)){
			throw new common_exception_InvalidArgumentType(__CLASS__, __FUNCTION__, 0, 'array', $properties);
        }
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getPropertiesValues($this, $properties/*, $last*/);
        
        // section 127-0-1-1-77557f59:12fa87873f4:-8000:00000000000014CD end

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

        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:0000000000001550 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->setType($this, $type);
        
        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:0000000000001550 end

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

        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:0000000000001553 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->removeType($this, $type);
        
        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:0000000000001553 end

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

        // section 127-0-1-1--72f5bf1f:12fd500f94d:-8000:0000000000001552 begin
    	foreach($this->getTypes() as $type){
        	if ($class->equals($type)){
        		$returnValue = true;
        		break;
        	}
        }
        // section 127-0-1-1--72f5bf1f:12fd500f94d:-8000:0000000000001552 end

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

        // section 127-0-1-1-7c36bc99:13092a153cd:-8000:0000000000001599 begin
        try{
        	$returnValue = count($this->getTypes())?true:false;
        }
        catch(Exception $e){
        	;//return false by default
        }
               
        // section 127-0-1-1-7c36bc99:13092a153cd:-8000:0000000000001599 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAllPropertyValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  array options
     * @return array
     */
    public function getAllPropertyValues( core_kernel_classes_Property $property, $options = array())
    {
        $returnValue = array();

        // section 127-0-1-1-3b11b49e:1323ea85daa:-8000:0000000000003DB0 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getAllPropertyValues($this, $property, $options);
        
        // section 127-0-1-1-3b11b49e:1323ea85daa:-8000:0000000000003DB0 end

        return (array) $returnValue;
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

        // section 127-0-1-1--9a19b46:135a440c85f:-8000:0000000000001BD6 begin
        $returnValue = $this->uriResource;
        // section 127-0-1-1--9a19b46:135a440c85f:-8000:0000000000001BD6 end

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

        // section 127-0-1-1--1f554305:136e33138a4:-8000:0000000000001DA5 begin
        if (is_null($resource)) {
        	throw new common_exception_Error('Null parameter in equals call on ressource '.$this->getUri());
        }
        $returnValue = $this->getUri() == $resource->getUri();
        // section 127-0-1-1--1f554305:136e33138a4:-8000:0000000000001DA5 end

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

        // section 127-0-1-1-4723dbde:1373bc88899:-8000:00000000000019E8 begin
        foreach($this->getTypes() as $type){
        	if ($class->equals($type) || $type->isSubClassOf($class)){
        		$returnValue = true;
        		break;
        	}
        }
        // section 127-0-1-1-4723dbde:1373bc88899:-8000:00000000000019E8 end

        return (bool) $returnValue;
    }
  
} /* end of class core_kernel_classes_Resource */

?>