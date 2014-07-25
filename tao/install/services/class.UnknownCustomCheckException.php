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
 * An exception describing that a requested custom check is unknown.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
class tao_install_services_UnknownCustomCheckException extends Exception{
    
    private $customCheckName;
    private $extensionName;
    
    /**
     * Creates a new instance.
     */
    public function __construct($customCheckName, $extensionName){
        parent::__construct("Unable to find Custom Check '${customCheckName}' in extension '${extensionName}'.");
        $this->setCustomCheckName($customCheckName);
        $this->setExtensionName($extensionName);
    }
    
    /**
     * Sets the Custom Check name that was requested but not found.
     * @param string $customCheckName A Custom Check name.
     */
    protected function setCustomCheckName($customCheckName){
        $this->customCheckName = $customCheckName;
    }
    
    /**
     * Gets the Custom Check name that was requested but not found.
     * @return string A Custom Check name.
     */
    public function getCustomCheckName(){
        return $this->customCheckName;
    }
    
    /**
     * Sets the Extension name where the Custom Check should have been found.
     * @param string $extensionName An Extension name.
     */
    protected function setExtensionName($extensionName){
        $this->extensionName = $extensionName;
    }
    
    /**
     * Gets the Extension name where the Custom Check should have been found.
     * @return string An Extension name.
     */
    public function getExtensionName(){
        return $this->extensionName;
    }
}
