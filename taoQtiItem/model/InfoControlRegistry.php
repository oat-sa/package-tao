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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
namespace oat\taoQtiItem\model;

use common_exception_Error;
use common_ext_ExtensionsManager;

/**
 * Registry for info controls
 * 
 * @author Sam <sam@taotesting.com>
 */
class InfoControlRegistry extends CustomInteractionRegistry
{
    /**
     * Key used to store the custom interactions in the config
     * 
     * @var string
     */
    const CONFIG_ID = 'info_control';
    
    protected function getConfigId(){
        return self::CONFIG_ID;
    }
    
    /**
     * Register a new custom interaction
     * 
     * @param string $qtiClass
     * @param string $phpClass
     * @throws common_exception_Error
     * @deprecated use set
     */
    public static function register($qtiClass, $phpClass) {
        InfoControlRegistry::getRegistry()->set($qtiClass,$phpClass);
    }
    
    /**
     * Get the php class that represents an info control from its class attribute
     * 
     * @param string $name
     * @deprecated set
     * @return string
     */
    public static function getInfoControlByName($name){
        InfoControlRegistry::getRegistry()->get($name);
    }
}
