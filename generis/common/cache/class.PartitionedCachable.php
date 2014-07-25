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
 * Generis Object Oriented API - common\cache\class.PartitionedCachable.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 18.01.2013, 12:12:06 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage cache
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Classes that implement this class claims their instances are serializable and
 * be identified by a unique serial string.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/interface.Serializable.php');

/* user defined includes */
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EDF-includes begin
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EDF-includes end

/* user defined constants */
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EDF-constants begin
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EDF-constants end

/**
 * Short description of class common_cache_PartitionedCachable
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage cache
 */
abstract class common_cache_PartitionedCachable
        implements common_Serializable
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute serial
     *
     * @access protected
     * @var string
     */
    protected $serial = '';

    /**
     * Short description of attribute serializedProperties
     *
     * @access protected
     * @var array
     */
    protected $serializedProperties = array();

    // --- OPERATIONS ---

    /**
     * Obtain a serial for the instance of the class that implements the
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getSerial()
    {
        $returnValue = (string) '';

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001ECB begin
        if (empty($this->serial)){
			$this->serial = $this->buildSerial();
		}
		$returnValue = $this->serial;
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001ECB end

        return (string) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EFD begin
    	if (!is_null($this->getCache())) {
        	$this->getCache()->put($this);
        }
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EFD end
    }

    /**
     * Gives the list of attributes to serialize by reflection.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function __sleep()
    {
        $returnValue = array();

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F05 begin
    	$this->serializedProperties = array();
        $reflection = new ReflectionClass($this);
		foreach($reflection->getProperties() as $property){
			//assuming that private properties don't contain serializables
			if(!$property->isStatic() && !$property->isPrivate()) {
				$propertyName = $property->getName();
				$containsSerializable = false;
				$value = $this->$propertyName;
				if (is_array($value)) {
					$containsNonSerializable = false;
					$serials = array();
					foreach ($value as $key => $subvalue) {
						if (is_object($subvalue) && $subvalue instanceof self) {
							$containsSerializable = true; 
							$serials[$key] = $subvalue->getSerial();
						} else {
							$containsNonSerializable = true;
						}
					}
					if ($containsNonSerializable && $containsSerializable) {
						throw new common_exception_Error('Serializable '.$this->getSerial().' mixed serializable and non serializable values in property '.$propertyName);
					}
				} else {
					if (is_object($value) && $value instanceof self) {
						$containsSerializable = true;
						$serials = $value->getSerial();
					}
				}
				if ($containsSerializable) {
					$this->serializedProperties[$property->getName()] = $serials;
				} else {
					$returnValue[] = $property->getName();
				}
			}
		}
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F05 end

        return (array) $returnValue;
    }

    /**
     * Short description of method __wakeup
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function __wakeup()
    {
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F08 begin
        foreach ($this->serializedProperties as $key => $value) {
			if (is_array($value)) {
				$restored = array();
				foreach ($value as $arrayKey => $arrayValue) {
					$restored[$arrayKey] = $this->getCache()->get($arrayValue);
				}
			} else {
				$restored = $this->getCache()->get($value);
			}
			$this->$key = $restored;
		}
		$this->serializedProperties = array();
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F08 end
    }

    /**
     * Short description of method _remove
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function _remove()
    {
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F0A begin
    	//usefull only when persistance is enabled
		if (!is_null($this->getCache())){
			//clean session
			$this->getCache()->remove($this->getSerial());
		}
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F0A end
    }

    /**
     * Short description of method getSuccessors
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getSuccessors()
    {
        $returnValue = array();

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F0C begin
    	$reflection = new ReflectionClass($this);
		foreach($reflection->getProperties() as $property){
			if(!$property->isStatic() && !$property->isPrivate()){
				$propertyName = $property->getName();
				$value = $this->$propertyName;
				if (is_array($value)) {
					foreach ($value as $key => $subvalue) {
						if (is_object($subvalue) && $subvalue instanceof self) {
								$returnValue[] = $subvalue;
						}
					}
				} elseif (is_object($value) && $value instanceof self) {
						$returnValue[] = $value;
					}
				}
		}
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F0C end

        return (array) $returnValue;
    }

    /**
     * Short description of method getPredecessors
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string classFilter
     * @return array
     */
    public function getPredecessors($classFilter = null)
    {
        $returnValue = array();

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F0E begin
    	foreach ($this->getCache()->getAll() as $serial => $instance) {
			
			if (($classFilter == null || $instance instanceof $classFilter)
				&& in_array($this, $instance->getSuccessors())) {
				$returnValue[] = $instance;
				break;
			}
		}
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F0E end

        return (array) $returnValue;
    }

    /**
     * Short description of method buildSerial
     *
     * @abstract
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    protected abstract function buildSerial();

    /**
     * Short description of method getCache
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_cache_Cache
     */
    public abstract function getCache();

} /* end of abstract class common_cache_PartitionedCachable */

?>