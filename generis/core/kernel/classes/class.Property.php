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

use oat\generis\model\data\ModelManager;

/**
 * uriProperty must be a valid property otherwis return false, add this as a
 * of uriProperty
 *
 * @access public
 * @author patrick.plichart@tudor.lu
 * @package generis
 
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
     * The widget the can be used to represents the property.
     * 
     * Dev note: this property is set to false because null is also a possible
     * valid value for this property. This will prevent the widget to be property
     * to be retrieved even if in cache, when no widget is set for the property.
     *
     * @access public
     * @var Property
     */
    public $widget = false;

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
     * @return core_kernel_persistence_PropertyInterface
     */
    private function getImplementation() {
        return ModelManager::getModel()->getRdfsInterface()->getPropertyImplementation();
    }
    

    /**
     * constructor
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string uri
     * @param  string debug
     * @return void
     */
    public function __construct($uri, $debug = '')
    {
        
		parent::__construct($uri,$debug);
		$this->lgDependent = null;
		$this->multiple = null;
        
    }

    /**
     * 
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    public function feed()
    {
        $this->getWidget();
        $this->getRange();
        $this->getDomain();
        $this->isLgDependent();
        
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
        if (is_null($this->domain)){
        	$this->domain = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
			$domainValues = $this->getPropertyValues(new core_kernel_classes_Property(RDFS_DOMAIN));
			foreach ($domainValues as $domainValue){
				$this->domain->add(new core_kernel_classes_Class($domainValue));
			}
		}
		$returnValue = $this->domain;
        

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
   
		if (is_null($this->range)){
			$rangeProperty = new core_kernel_classes_Property(RDFS_RANGE,__METHOD__);
            $rangeValues = $this->getPropertyValues($rangeProperty);

            if(sizeOf($rangeValues)>0){
                $returnValue = new core_kernel_classes_Class($rangeValues[0]);
            }
			$this->range = $returnValue;
		}
		$returnValue = $this->range;
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
        $returnValue = $this->getImplementation()->setRange($this, $class);
        if ($returnValue){
        	$this->range = $class;
        }
        return (bool) $returnValue;
    }

    /**
     * Get the Property object corresponding to the widget of this Property.
     *
     * @author Cédric Alfonsi <cedric.alfonsi@tudor.lu>
     * @author Antoine Delamarre <antoine.delamarre@vesperiagroup.com>
     * @author Jérôme Bogaerts <jerome@taotesting.com>
     * @return core_kernel_classes_Property The Property object corresponding to the widget of this Property.
     */
    public function getWidget()
    {
        if ($this->widget === false) {
			$this->widget = $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_WIDGET));
		}
		
		return $this->widget;
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
        if (is_null($this->lgDependent )){

            $this->lgDependent  = helpers_PropertyLgCacheHelper::getLgDependencyCache($this->getUri());

            if (is_null($this->lgDependent)) {
                $lgDependentProperty = new core_kernel_classes_Property(PROPERTY_IS_LG_DEPENDENT,__METHOD__);
                $lgDependent = $this->getOnePropertyValue($lgDependentProperty);


			 
    			if (is_null($lgDependent) || !$lgDependent instanceof  core_kernel_classes_Resource){
    				$returnValue = false;
    			}
    			else{
    				$returnValue = ($lgDependent->getUri() == GENERIS_TRUE);
    			}
                helpers_PropertyLgCacheHelper::setLgDependencyCache($this->getUri(), $returnValue);    
            	$this->lgDependent = $returnValue;
            }
        }
 
        $returnValue = $this->lgDependent;
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
        $this->getImplementation()->setLgDependent($this, $isLgDependent);
        helpers_PropertyLgCacheHelper::setLgDependencyCache($this->getUri(), $isLgDependent);
    	$this->lgDependent = $isLgDependent;
        
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
        
    	$this->getImplementation()->setMultiple($this, $isMultiple);
    	$this->multiple = $isMultiple;
        
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
        $returnValue = $this->getImplementation()->delete($this, $deleteReference);
        return (bool) $returnValue;
    }

}
