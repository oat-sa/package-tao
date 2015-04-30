<?php
use oat\tao\model\ClientLibRegistry;
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
*/

/**
 * Generates client side configuration.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @package tao
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class tao_actions_ClientConfig extends tao_actions_CommonModule {

    /**
     * Get the require.js' config file
     */
    public function config() {
        $this->setContentHeader('application/javascript');
        
        //get extension paths to set up aliases dynamically
        $extensionsAliases = ClientLibRegistry::getRegistry()->getLibAliasMap();
                
        $this->setData('extensionsAliases', $extensionsAliases);
        
        $extensionManager = common_ext_ExtensionsManager::singleton();
        $langCode = tao_helpers_I18n::getLangCode();


        //loads the URLs context
        $base_www = BASE_WWW;
        $base_url = BASE_URL;
        if($this->hasRequestParameter('extension')){
            $extension = $extensionManager->getExtensionById($this->getRequestParameter('extension'));
            if(!is_null($extension)){
                $base_www = $extension->getConstant('BASE_WWW');
                $base_url = $extension->getConstant('BASE_URL');
            }
        }

        $mediaSources = \oat\tao\model\media\MediaSource::getMediaBrowserSources();
        
        //set contextual data
        $this->setData('locale', $langCode);
        
        if(strpos($langCode, '-') > 0){
            $lang = strtolower(substr($langCode, 0, strpos($langCode, '-')));
        } else {
            $lang = strtolower($langCode);
        }
        $this->setData('lang',              $lang);
        $this->setData('extension',         $this->getRequestParameter('extension'));
        $this->setData('module',            $this->getRequestParameter('module'));
        $this->setData('action',            $this->getRequestParameter('action'));
        $this->setData('base_www',          $base_www);
        $this->setData('base_url',          $base_url);
        $this->setData('shownExtension',    $this->getRequestParameter('shownExtension'));
        $this->setData('shownStructure',    $this->getRequestParameter('shownStructure'));
        $this->setData('client_timeout',    $this->getClientTimeout());
        $this->setData('mediaSources',      $mediaSources);

        $this->setView('client_config.tpl');
    }
}
?>
