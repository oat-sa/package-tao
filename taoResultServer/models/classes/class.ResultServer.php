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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author "Patrick Plichart, <patrick@taotesting.com>"
 * @package taoResultServer
 */


class taoResultServer_models_classes_ResultServer
{

    /**
     * @var core_kernel_classes_Resource
     */
    private $resultServer; 
    
    /**
     * @var taoResultServer_models_classes_WritableResultStorage A result storage
     */
    private $storageContainer; 
    
    private $implementations;

    /**
     *
     * @param array callOptions an array of parameters sent to the results storage configuration
     * @param mixed $resultServer
     * @param string uri or resource
     */
    public function __construct($resultServer, $additionalStorages = array())
    {
        $this->implementations = array();
        
        if (is_object($resultServer) and (get_class($resultServer) == 'core_kernel_classes_Resource')) {
            $this->resultServer = $resultServer;
        } else {
            if (common_Utils::isUri($resultServer)) {
                $this->resultServer = new core_kernel_classes_Resource($resultServer);
            }
        }
        // the static storages
        if ($this->resultServer->getUri() != TAO_VOID_RESULT_SERVER) {
            $resultServerModels = $this->resultServer->getPropertyValues(new core_kernel_classes_Property(TAO_RESULTSERVER_MODEL_PROP));
            if ((! isset($resultServerModels)) or (count($resultServerModels) == 0)) {
                throw new common_Exception("The result server is not correctly configured (Resource definition)");
            }
            foreach ($resultServerModels as $resultServerModelUri) {
                $resultServerModel = new core_kernel_classes_Resource($resultServerModelUri);
                $this->addImplementation($resultServerModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_RESULTSERVER_MODEL_IMPL_PROP))->literal);
            }
        }
        if (! is_null($additionalStorages)) {
            // the dynamic storages
            foreach ($additionalStorages as $additionalStorage) {
                $this->addImplementation($additionalStorage["implementation"], $additionalStorage["parameters"]);
            }
        }
        
        common_Logger::i("Result Server Initialized using defintion:" . $this->resultServer->getUri());
        // sets the details required depending on the type of storage
    }

    /**
     * @access public
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @param unknown $className
     * @param unknown $options
     */
    public function addImplementation($className, $options = array())
    {
        $this->implementations[] = array(
            "className" => $className,
            "params" => $options
        );
    }

    /**
     * @access public
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @return taoResultServer_models_classes_ResultStorageContainer
     */
    public function getStorageInterface()
    {
        $storageContainer = new taoResultServer_models_classes_ResultStorageContainer($this->implementations);
        $storageContainer->configure($this->resultServer);
        return $storageContainer;
    }
}
?>