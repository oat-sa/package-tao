<?php
/**
 * 
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
namespace oat\tao\model\websource;

use common_ext_ExtensionsManager;
use common_Exception;
/**
 * Websource manager
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class WebsourceManager
{	
    const CONFIG_PREFIX = 'websource_';

    private static $instance = null;
    
    public static function singleton() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private $websources = array();
    
    private function __construct() {
    }
    
    public function getWebsource($key) {
        if (!isset($this->websources[$key])) {
            $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
            $conf = $ext->getConfig(self::CONFIG_PREFIX.$key);
            if (!is_array($conf) || !isset($conf['className'])) {
                throw new WebsourceNotFound('Undefined websource '.$key);
            }
            $className = $conf['className'];
            $options = isset($conf['options']) ? $conf['options'] : array();
            $this->websources[$key] = new $className($options);
        }
        return $this->websources[$key];
    }
    
    public function addWebsource($websource) {
        $key = $websource->getId();
        if (is_null($key)) {
            throw new common_Exception('Missing identifier for websource');
        }
        $this->websources[$websource->getId()] = $websource;
        $conf = array(
        	'className' => get_class($websource),
            'options' => $websource->getOptions()
        );
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $ext->setConfig(self::CONFIG_PREFIX.$key, $conf);
    }
    
    public function removeWebsource($websource) {
        if (!isset($this->websources[$websource->getId()])) {
            throw new common_Exception('Attempting to remove inexistent '.$websource->getId());
        }
        unset($this->websources[$websource->getId()]);
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $conf = $ext->unsetConfig(self::CONFIG_PREFIX.$websource->getId());
    }

}