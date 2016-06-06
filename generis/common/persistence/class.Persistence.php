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
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package generis
 *
 */
abstract class common_persistence_Persistence
{
    /**
     * Driver of the persistence
     * 
     * @var common_persistence_Driver
     */
    private $driver;
    
    /**
     * Persistence parameters
     * 
     * @var array
     */
    private $params = array();
    
    public static function getPersistence($driverId) {
        $returnValue = common_persistence_Manager::getPersistence($driverId);
        $class = get_called_class();
        if (!$returnValue instanceof $class) {
            common_Logger::w('Got a ',get_class($returnValue).' instead of '.$class);
        }
        return $returnValue;
    }

    /**
     * Constructor
     * 
     * @access public
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param array $params
     * @param common_persistence_driver $driver
     */
    public function __construct($params = array(), common_persistence_driver $driver){
        $this->setParams($params);
        $this->setDriver($driver);
    }

    /**
     * Retrieve persistence's driver
     * 
     * @access public
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @return common_persistence_Driver
     */
    public function getDriver(){
        return $this->driver;
    }

    /**
     * Set the persistence
     * 
     * @access protected
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param common_persistence_Driver $driver
     */
    protected  function setDriver(common_persistence_Driver $driver){
        $this->driver=$driver;
    }

    /**
     * Retrieve persistence's parameters
     * 
     * @access protected
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @return multitype:
     */
    protected function getParams(){
        return $this->params;
    }

    /**
     * set persistence's parameters
     * 
     * @access protected
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param unknown $params
     */
    protected function setParams($params){
        $this->params = $params;
    }
}

