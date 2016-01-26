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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model;

use \common_exception_Error;
use \common_ext_ExtensionsManager;

class HookRegistry extends AbstractInteractionRegistry
{
    
    /**
     * Key used to store the custom interactions in the config
     * 
     * @var string
     */
    const CONFIG_ID = 'hook';
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoQtiItem\model\AbstractInteractionRegistry::getConfigId()
     */
    protected function getConfigId(){
        return self::CONFIG_ID;
        }
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoQtiItem\model\AbstractInteractionRegistry::getInteractionClass()
     */
    protected function getInteractionClass(){
        return 'oat\taoQtiItem\model\Hook';
    }
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $key
     * @param string $class
     * @deprecated use set directly
     */
    public static function add($key,$phpClass){
        HookRegistry::getRegistry()->set($key,$phpClass);
    }
    
}