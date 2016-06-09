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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

namespace oat\tao\helpers;

use oat\oatbox\service\ServiceManager;
use oat\tao\model\asset\AssetService;

class Template {

    /**
     * Expects a relative url to the image as path
     * if extension name is omitted the current extension is used
     * 
     * @param string $path
     * @param string $extensionId
     * @return string
     */
    public static function img($path, $extensionId = null) {
        if (is_null($extensionId)) {
            $extensionId = \Context::getInstance()->getExtensionName();
        }

        return self::getAssetService()->getAsset('img/'.$path, $extensionId);
    }
    
    /**
     * Expects a relative url to the css as path
     * if extension name is omitted the current extension is used
     * 
     * @param string $path
     * @param string $extensionId
     * @return string
     */
    public static function css($path, $extensionId = null) {
        if (is_null($extensionId)) {
            $extensionId = \Context::getInstance()->getExtensionName();
        }
        return self::getAssetService()->getAsset('css/'.$path, $extensionId);
    }
    
    /**
     * Expects a relative url to the java script as path
     * if extension name is omitted the current extension is used
     * 
     * @param string $path
     * @param string $extensionId
     * @return string
     */
    public static function js($path, $extensionId = null) {
        if (is_null($extensionId)) {
            $extensionId = \Context::getInstance()->getExtensionName();
        }
        return self::getAssetService()->getAsset('js/'.$path, $extensionId);
    }
    
    /**
     * Expects a relative url to the template that is to be included as path
     * if extension name is omitted the current extension is used
     *
     * @param string $path
     * @param string $extensionId
     * @param array $data bind additional data to the context
     * @return string
     */
    public static function inc($path, $extensionId = null, $data = array()) {
        $context = \Context::getInstance();
        if (!is_null($extensionId) && $extensionId != $context->getExtensionName()) {
            // template is within different extension, change context
            $formerContext = $context->getExtensionName();
            $context->setExtensionName($extensionId);
        }

        if(count($data) > 0){
            \RenderContext::pushContext($data);
        }
        
        $absPath = self::getTemplate($path, $extensionId);
        if (file_exists($absPath)) {
            include($absPath);
        } else {
            \common_Logger::w('Failed to include "'.$absPath.'" in template');
        }
        // restore context
        if (isset($formerContext)) {
            $context->setExtensionName($formerContext);
        }
    }

    /**
     * @param $path
     * @param null $extensionId
     * @return string
     */
    public static function getTemplate($path, $extensionId = null) {
        $extensionId = is_null($extensionId) ? \Context::getInstance()->getExtensionName() : $extensionId;
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById($extensionId);
        return $ext->getConstant('DIR_VIEWS').'templates'.DIRECTORY_SEPARATOR.$path;
    }

    /**
     * @FIXME get_data and has_data should be used exclusively inside templates (not namespaced)
     * @return array|bool
     */
    public static function getMessages() {
        $messages = array();
        if(has_data('errorMessage')){
            $messages['error'] = get_data('errorMessage');
        }
        if(has_data('message')){
            $messages['info'] = get_data('message');
        }
        return !!count($messages) ? $messages : false;
    }

    /**
     * @return AssetService
     * @throws \common_Exception
     * @throws \oat\oatbox\service\ServiceNotFoundException
     */
    private static function getAssetService()
    {
        return ServiceManager::getServiceManager()->get(AssetService::SERVICE_ID);
    }
}
