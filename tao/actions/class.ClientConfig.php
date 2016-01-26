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
*/
use oat\tao\model\ClientLibRegistry;
use oat\tao\model\ClientLibConfigRegistry;
use oat\tao\model\ThemeRegistry;
use oat\tao\model\asset\AssetService;
use \oat\oatbox\service\ServiceManager;

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

        $libConfigs = ClientLibConfigRegistry::getRegistry()->getMap();
        $this->setData('libConfigs', $libConfigs);

        $themesAvailable = ThemeRegistry::getRegistry()->getAvailableThemes();
        $this->setData('themesAvailable', json_encode($themesAvailable));

        $extensionManager = common_ext_ExtensionsManager::singleton();
        $langCode = tao_helpers_I18n::getLangCode();


        //loads the URLs context
        /** @var AssetService $assetService */
        $assetService = ServiceManager::getServiceManager()->get(AssetService::SERVICE_ID);
        $tao_base_www = $assetService->getJsBaseWww('tao');

        $extensionId = ($this->hasRequestParameter('extension')) ? $this->getRequestParameter('extension') : \Context::getInstance()->getExtensionName();
        $extension = $extensionManager->getExtensionById($extensionId);
        $base_www = $assetService->getJsBaseWww($extensionId);
        $base_url = $extension->getConstant('BASE_URL');


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
        $this->setData('tao_base_www',      $tao_base_www);
        $this->setData('base_url',          $base_url);
        $this->setData('shownExtension',    $this->getRequestParameter('shownExtension'));
        $this->setData('shownStructure',    $this->getRequestParameter('shownStructure'));
        $this->setData('client_timeout',    $this->getClientTimeout());

        $this->setView('client_config.tpl');
    }
}
?>
