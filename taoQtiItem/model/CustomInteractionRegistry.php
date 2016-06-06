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
 * Registry for custom interactions 
 * 
 * @author Joel Bout <joel@taotestinf.com>
 */
class CustomInteractionRegistry extends AbstractInteractionRegistry
{
    /**
     * Key used to store the custom interactions in the config
     * 
     * @var string
     */
    const CONFIG_ID = 'custom_interaction';
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return string
     */
    protected function getConfigId(){
        return self::CONFIG_ID;
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return string
     */
    protected function getInteractionClass(){
        return 'oat\taoQtiItem\model\qti\interaction\CustomInteraction';
    }
    
    
    /**
     * Register a new custom interaction
     * 
     * @param string $qtiClass
     * @param string $phpClass
     * @deprecated
     * @throws common_exception_Error
     */
    public static function register($qtiClass, $phpClass) {
        if (!class_exists($phpClass)) {
            throw new common_exception_Error('Custom interaction class '.$phpClass.' not found');
        }
        if (!is_subclass_of($phpClass, 'oat\taoQtiItem\model\qti\interaction\CustomInteraction')) {
            throw new common_exception_Error('Class '.$phpClass.' not a subclass of CustomInteraction');
        }
        $taoQtiItem = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
        $map = self::getCustomInteractions();
        $map[$qtiClass] = $phpClass;
        $taoQtiItem->setConfig(self::CONFIG_ID, $map);
    }
    
    /**
     * Returns a list of registered custom interactions.
     * 
     * With the qti classes as keys and the php classnames that
     * implementat these interactions as values
     * 
     * @deprecated
     * @return array
     */
    public static function getCustomInteractions() {
        $taoQtiItem = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
        $map = $taoQtiItem->getConfig(self::CONFIG_ID);
        return is_array($map) ?  $map : array();
    }
    
    /**
     * Get the php class that represents a custom interaction from its class attribute
     * 
     * @param string $name
     * @deprecated
     * @return string
     * 
     */
    public static function getCustomInteractionByName($name){
        $all = self::getCustomInteractions();
        if(isset($all[$name])){
            return $all[$name];
        }
        return '';
    }
}
