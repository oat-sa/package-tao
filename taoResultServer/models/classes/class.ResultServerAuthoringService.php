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
 *               2013-2014 (update and modification) Open Assessment Technologies SA
 */

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 * 
 * @author Joel Bout, <joel.bout@tudor.lu>
 * 
 */
class taoResultServer_models_classes_ResultServerAuthoringService 
    extends tao_models_classes_ClassService
{

    const DEFAULT_RESULTSERVER_KEY = 'default_resultserver';
    
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

	/** (non-PHPdoc)
	 * @see tao_models_classes_ClassService::getRootClass()
	 */
	public function getRootClass() {
	    return $this->resultServerClass;

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
            $clazz = $this->getRootClass();
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
            if ($this->isResultServerClass($clazz) && $clazz->getUri() != $this->getRootClass()->getUri()) {
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
        
        if ($clazz->getUri() == $this->getRootClass()->getUri()) {
            $returnValue = true;
        } else {
            foreach ($this->getRootClass()->getSubClasses(true) as $subclass) {
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
        
        if (empty($uri) && ! is_null($this->getRootClass())) {
            $returnValue = $this->getRootClass();
        } else {
            $clazz = new core_kernel_classes_Class($uri);
            if ($this->isResultServerClass($clazz)) {
                $returnValue = $clazz;
            }
        }
        
        return $returnValue;
    }
    /**
     * 
     * @return array readable and writable storages of results
     */
    public function getResultStorages(){
        $storageClass = new core_kernel_classes_Class(TAO_RESULTSERVER_MODEL_CLASS);
        
        $readableStorages = array();
        $writableStorages = array();
        
        foreach ($storageClass->getInstances() as $storage) {
            $impl = $storage->getUniquePropertyValue(new core_kernel_classes_Property(TAO_RESULTSERVER_MODEL_IMPL_PROP));
            $interfaces = class_implements($impl->__toString());
            if (in_array('taoResultServer_models_classes_ReadableResultStorage', $interfaces)) {
                $readableStorages[] = $storage;
            }
            if (in_array('taoResultServer_models_classes_WritableResultStorage', $interfaces)) {
                $writableStorages[] = $storage;
            }
        }
        return array("r"    =>  $readableStorages, "w"  =>  $writableStorages); 
    }
    
    /**
     * Migrates data between source and target storage
     * 
     * @param array core_kernel_classes_Resource $sourceStorage 
     * @param array core_kernel_classes_Resource core_kernel_classes_Resource
     * 
     * @return array
     */
    public function migrateData( $sourceStorages, $targetStorages){
        
        $sourceImpl = array();
        $targetImpl = array();

        $returnValue = array('success' => false);

        // holds the result of each storage
        $returnData  = array();

        if (!is_array($sourceStorages) || !is_array($targetStorages)) {
            // $sourceStorages must be an array of storages
            $return['status'] = __('Invalid request');
            return $return;
        }


        foreach ($sourceStorages as $sourceStorage) {
            $sourceStorageResource = new core_kernel_classes_Resource($sourceStorage);
            $implLiteral = $sourceStorageResource->getUniquePropertyValue(new core_kernel_classes_Property(TAO_RESULTSERVER_MODEL_IMPL_PROP));
            $impl = $implLiteral->__toString(); 
            $interfaces = class_implements($impl);
            if (!(in_array('taoResultServer_models_classes_ReadableResultStorage', $interfaces))) {
                $return['status'] = __($sourceStorage. 'does not implement ReadableResultStorage');
                return $return;
            } else {
                $sourceImpl[] = new $impl;
                $returnData[] = array('uri' => $sourceStorage);
            }
        }
        
        foreach ($targetStorages as $targetStorage) {
            $targetStorageResource = new core_kernel_classes_Resource($targetStorage);
            $implLiteral = $targetStorageResource->getUniquePropertyValue(new core_kernel_classes_Property(TAO_RESULTSERVER_MODEL_IMPL_PROP));
            $impl = $implLiteral->__toString(); 
            $interfaces = class_implements($impl);
            if (!(in_array('taoResultServer_models_classes_WritableResultStorage', $interfaces))) {
                $return['status'] = __($targetStorage. 'does not implement WritableResultStorage');
                return $return;
            } else {
                $targetImpl[] = new $impl;
            }
        }

        foreach ($sourceImpl as $key => $storageSImpl) {

            //migrate test taker data
            $allTestTakerIds = $storageSImpl->getAllTestTakerIds();
            foreach ($targetImpl as $storageTImpl) {
                foreach ($allTestTakerIds as $testTakerId) {
                    $storageTImpl->storeRelatedTestTaker($testTakerId["deliveryResultIdentifier"], $testTakerId["testTakerIdentifier"]);
                }
            }
           
            
            //migrate Delivery data
            $allDeliveryIds = $storageSImpl->getAllDeliveryIds();
            foreach ($targetImpl as $storageTImpl) {
                foreach ($allDeliveryIds as $deliveryId) {
                    $storageTImpl->storeRelatedDelivery($deliveryId["deliveryResultIdentifier"], $deliveryId["deliveryIdentifier"]);
                }
            }
            
            //migrate all service call submitted variables
            $callIds = $storageSImpl->getAllCallIDs();//o(n)
            foreach ($callIds as $callId){
                $variables = $storageSImpl->getVariables($callId);
                foreach ($variables as $variableIdentifier=>$observations) {

                        foreach ($observations as $observation) {
                        
                            foreach ($targetImpl as $storageTImpl) {                              
                                if (isset($observation->callIdItem)) {  //item level variable
                                        $storageTImpl->storeItemVariable(
                                        $observation->deliveryResultIdentifier,
                                        $observation->test,
                                        $observation->item,
                                        $observation->variable,
                                        $observation->callIdItem );
                                } else { //test level variable
                                        //print_r($observation);
                                        $storageTImpl->storeTestVariable(
                                        $observation->deliveryResultIdentifier,
                                        $observation->test,
                                        $observation->variable,
                                        $observation->callIdTest );
                                        
                                    
                                }
                            }
                            
                        }
                }
                
            }

            $returnData[$key]['testTakers'] = count($allTestTakerIds);
            $returnData[$key]['deliveries'] = count($allDeliveryIds);
            $returnData[$key]['callIds']    = count($callIds);

        }

        $returnValue['success'] = true;
        $returnValue['status'] = __('Migration Successful');
        $returnValue['data'] = $returnData;
        return $returnValue;
    }

    /**
     * Return the default result server to use
     * 
     * @return core_kernel_classes_Resource
     */
    public function getDefaultResultServer()
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoResultServer');
        if ($ext->hasConfig(self::DEFAULT_RESULTSERVER_KEY)) {
            $uri = $ext->getConfig(self::DEFAULT_RESULTSERVER_KEY);
        } else {
            $uri = TAO_VOID_RESULT_SERVER;
        }
        
        return new core_kernel_classes_Resource($uri);
    }
    
    /**
     * Sets the default result server to use
     * 
     * @param core_kernel_classes_Resource $resultServer
     */
    public function setDefaultResultServer(core_kernel_classes_Resource $resultServer) {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoResultServer');
        $ext->setConfig(self::DEFAULT_RESULTSERVER_KEY, $resultServer->getUri());
    }
}