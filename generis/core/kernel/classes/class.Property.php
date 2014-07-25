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
 * uriProperty must be a valid property otherwis return false, add this as a
 * of uriProperty
 *
 * @author patrick.plichart@tudor.lu
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Resource implements rdf:resource container identified by an uri (a string).
 * Methods enable meta data management for this resource
 *
 * @author patrick.plichart@tudor.lu
 * @see http://www.w3.org/RDF/
 * @version v1.0
 */
require_once('core/kernel/classes/class.Resource.php');

/* user defined includes */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000769-includes begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000769-includes end

/* user defined constants */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000769-constants begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000769-constants end

/**
 * uriProperty must be a valid property otherwis return false, add this as a
 * of uriProperty
 *
 * @access public
 * @author patrick.plichart@tudor.lu
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_Property
    extends core_kernel_classes_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The property domain defines the classes the property is attached to.
     *
     * @access public
     * @var ContainerCollection
     */
    public $domain = null;

    /**
     * The property's range defines either the possibles class' instances 
     * or a literal value if the range is the Literal class
     *
     * @access public
     * @var Class
     */
    public $range = null;

    /**
     * The widget the can be used to represents the property
     *
     * @access public
     * @var Property
     */
    public $widget = null;

    /**
     * Short description of attribute lgDependent
     *
     * @access public
     * @var boolean
     */
    public $lgDependent = false;

    /**
     * Short description of attribute multiple
     *
     * @access public
     * @var boolean
     */
    public $multiple = false;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string uri
     * @param  string debug
     * @return void
     */
    public function __construct($uri, $debug = '')
    {
        // section 10-5-2-6--89b5018:11b0b8ddfb0:-8000:0000000000000D5E begin
		parent::__construct($uri,$debug);
		$this->lgDependent = null;
		$this->multiple = null;
        // section 10-5-2-6--89b5018:11b0b8ddfb0:-8000:0000000000000D5E end
    }

    /**
     * Short description of method feed
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return void
     */
    public function feed()
    {
        // section 10-5-2-6--89b5018:11b0b8ddfb0:-8000:0000000000000D60 begin
		
    	parent::feed();
		$this->getWidget();
		$this->getRange();
		$this->getDomain();
		$this->isLgDependent();
		
        // section 10-5-2-6--89b5018:11b0b8ddfb0:-8000:0000000000000D60 end
    }

    /**
     * Short description of method getSubProperties
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  boolean recursive
     * @return array
     */
    public function getSubProperties($recursive = false)
    {
        $returnValue = array();

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000780 begin
        
        $returnValue = core_kernel_persistence_PropertyProxy::singleton()->getSubProperties($this, $recursive);
        
        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000780 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setSubPropertyOf
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Property property
     * @return boolean
     */
    public function setSubPropertyOf( core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000ABC begin
       
        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000ABC end

        return (bool) $returnValue;
    }

    /**
     * return classes that are described by this property
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getDomain()
    {
        $returnValue = null;

        // section 10-13-1--31--64270bf:11918ad765e:-8000:0000000000000972 begin
        if (is_null($this->domain)){
        	$this->domain = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
			$domainValues = $this->getPropertyValues(new core_kernel_classes_Property(RDFS_DOMAIN));
			foreach ($domainValues as $domainValue){
				$this->domain->add(new core_kernel_classes_Class($domainValue));
			}
		}
		$returnValue = $this->domain;
        // section 10-13-1--31--64270bf:11918ad765e:-8000:0000000000000972 end

        return $returnValue;
    }

    /**
     * Short description of method setDomain
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function setDomain( core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AB4 begin
        
        if(!is_null($class)){
        	foreach($this->getDomain()->getIterator() as $domainClass){
        		if ($class->equals($domainClass)) {
        			$returnValue = true;
        			break;
        		}
        	}
        	if(!$returnValue){
        		$this->setPropertyValue(new core_kernel_classes_Property(RDFS_DOMAIN), $class->getUri());
        		if(!is_null($this->domain)){
        			$this->domain->add($class);
        		}
        		$returnValue = true;
        	}
        }
        
        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AB4 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRange
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRange()
    {
        $returnValue = null;

        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000ABA begin
        
		if (is_null($this->range)){
			$rangeProperty = new core_kernel_classes_Property(RDFS_RANGE,__METHOD__);
                        $rangeValues = $this->getPropertyValues($rangeProperty);

                        if(sizeOf($rangeValues)>0){
                                $returnValue = new core_kernel_classes_Class($rangeValues[0]);
                        }
			$this->range = $returnValue;
		}
		$returnValue = $this->range;
		
        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000ABA end

        return $returnValue;
    }

    /**
     * Short description of method setRange
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function setRange( core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AB7 begin
        
        $returnValue = core_kernel_persistence_PropertyProxy::singleton()->setRange($this, $class);
        if ($returnValue){
        	$this->range = $class;
        }
        
        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AB7 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getWidget
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_Property
     */
    public function getWidget()
    {
        $returnValue = null;

        // section 10-5-2-6--89b5018:11b0b8ddfb0:-8000:0000000000000D5C begin
		
        if (!(isset($this->widget))){
			$returnValue = $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_WIDGET));
		}
		
        // section 10-5-2-6--89b5018:11b0b8ddfb0:-8000:0000000000000D5C end

        return $returnValue;
    }

    /**
     * Is the property translatable?
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function isLgDependent()
    {
        $returnValue = (bool) false;

        // section 10-13-1--99--152a2f30:1201eae099d:-8000:000000000000157A begin

        if (is_null($this->lgDependent )){
	        $lgDependentProperty = new core_kernel_classes_Property(PROPERTY_IS_LG_DEPENDENT,__METHOD__);
			$lgDependent = $this->getOnePropertyValue($lgDependentProperty);
			 
			if (is_null($lgDependent)){
				$returnValue = false;
			}
			else{
				$returnValue = ($lgDependent->getUri() == GENERIS_TRUE);
			}
                
        	$this->lgDependent = $returnValue;
        }
 
        $returnValue = $this->lgDependent;
        
        // section 10-13-1--99--152a2f30:1201eae099d:-8000:000000000000157A end

        return (bool) $returnValue;
    }

    /**
     * Set mannually if a property can be translated
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  boolean isLgDependent
     * @return mixed
     */
    public function setLgDependent($isLgDependent)
    {
        // section 10-13-1--99--152a2f30:1201eae099d:-8000:000000000000157E begin
        core_kernel_persistence_PropertyProxy::singleton()->setLgDependent($this, $isLgDependent);
    	$this->lgDependent = $isLgDependent;
        // section 10-13-1--99--152a2f30:1201eae099d:-8000:000000000000157E end
    }

    /**
     * Check if a property can have multiple values.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function isMultiple()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--7856c56:12f911716ca:-8000:00000000000014C8 begin
        
        if(is_null($this->multiple )){
        	$multipleProperty = new core_kernel_classes_Property(PROPERTY_MULTIPLE,__METHOD__);
			$multiple = $this->getOnePropertyValue($multipleProperty);
			 
			if(is_null($multiple)){
				$returnValue = false;
			}
			else{
				$returnValue = ($multiple->getUri() == GENERIS_TRUE);
			}
        	$this->multiple = $returnValue;
        }
 
        $returnValue = $this->multiple;
        
        // section 127-0-1-1--7856c56:12f911716ca:-8000:00000000000014C8 end

        return (bool) $returnValue;
    }

    /**
     * Define mannualy if a property is multiple or not.
     * Usefull on just created property.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  boolean isMultiple
     * @return mixed
     */
    public function setMultiple($isMultiple)
    {
        // section 127-0-1-1-2ada041a:12fde2cecc0:-8000:00000000000016F8 begin
    	core_kernel_persistence_PropertyProxy::singleton()->setMultiple($this, $isMultiple);
    	$this->multiple = $isMultiple;
        // section 127-0-1-1-2ada041a:12fde2cecc0:-8000:00000000000016F8 end
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete($deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--330ca9de:1318ac7ca9f:-8000:0000000000001646 begin
        
        $returnValue = core_kernel_persistence_PropertyProxy::singleton()->delete($this, $deleteReference);
        
        // section 127-0-1-1--330ca9de:1318ac7ca9f:-8000:0000000000001646 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_classes_Property */

?>