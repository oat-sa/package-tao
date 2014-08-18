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
 * An Exception which states that a requested Service cannot be found.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
class tao_install_services_UnknownServiceException extends Exception{
    
    /**
     * The requested Service name.
     */
    private $serviceName;
    
    /**
     * Creates a new UnknownServiceException.
     * @param string $serviceName The name of the requested Service.
     */
    public function __construct($serviceName = null){
        if (!empty($serviceName)){
            parent::__construct("Service not found.");
        }
        else {
            parent::__construct("Service '${serviceName} not found.");
        }
        
        $this->setServiceName($serviceName);
    }

    /**
     * Sets the requested Service name.
     * @param string $serviceName A Service name.
     * @return void
     */
    protected function setServiceName($serviceName){
        $this->serviceName = $serviceName;
    }
    
    /**
     * Gets the requested Service name.
     * @return string A Service name.
     */
    public function getServiceName(){
        return $this->serviceName;
    }
}
?>