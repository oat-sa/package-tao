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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 *
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 * 
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoResultServer
 * @subpackage models_classes
 */
class taoResultServer_models_classes_ResultServerAuthoringService extends tao_models_classes_GenerisService
{

    /**
     *
     * @access protected
     * @var core_kernel_classes_Class
     */
    protected $resultServerClass = null;
    
    /**
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->resultServerClass = new core_kernel_classes_Class(TAO_RESULTSERVER_CLASS);
    }

    /**
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param core_kernel_classes_Class clazz
     * @param string label
     * @param array properties
     * @return core_kernel_classes_Class
     */
    public function createResultServerClass(core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    {
        $returnValue = null;
        
        if (is_null($clazz)) {
            $clazz = $this->resultServerClass;
        }
        
        if ($this->isResultServerClass($clazz)) {
            
            $resultServerClass = $this->createSubClass($clazz, $label); // call method form TAO_model_service
            
            foreach ($properties as $propertyName => $propertyValue) {
                $myProperty = $resultServerClass->createProperty($propertyName, $propertyName . ' ' . $label . ' resultServer property from ' . get_class($this) . ' the ' . date('Y-m-d h:i:s'));
            }
            $returnValue = $resultServerClass;
        }
        
        return $returnValue;
    }

    /**
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param core_kernel_classes_Resource resultServer
     * @return boolean
     */
    public function deleteResultServer(core_kernel_classes_Resource $resultServer)
    {
        $returnValue = (bool) false;
        
        if (! is_null($resultServer)) {
            $returnValue = $resultServer->delete();
        }
        
        return (bool) $returnValue;
    }

    /**
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param core_kernel_classes_Class clazz
     * @return boolean
     */
    public function deleteResultServerClass(core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;
        
        if (! is_null($clazz)) {
            if ($this->isResultServerClass($clazz) && $clazz->getUri() != $this->resultServerClass->getUri()) {
                $returnValue = $clazz->delete();
            }
        }
        
        return (bool) $returnValue;
    }

    /**
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param core_kernel_classes_Class clazz
     * @return boolean
     */
    public function isResultServerClass(core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;
        
        if ($clazz->getUri() == $this->resultServerClass->getUri()) {
            $returnValue = true;
        } else {
            foreach ($this->resultServerClass->getSubClasses(true) as $subclass) {
                if ($clazz->getUri() == $subclass->getUri()) {
                    $returnValue = true;
                    break;
                }
            }
        }
        
        return (bool) $returnValue;
    }

    /**
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param string uri
     * @return core_kernel_classes_Class
     */
    public function getResultServerClass($uri = '')
    {
        $returnValue = null;
        
        if (empty($uri) && ! is_null($this->resultServerClass)) {
            $returnValue = $this->resultServerClass;
        } else {
            $clazz = new core_kernel_classes_Class($uri);
            if ($this->isResultServerClass($clazz)) {
                $returnValue = $clazz;
            }
        }
        
        return $returnValue;
    }
}

?>