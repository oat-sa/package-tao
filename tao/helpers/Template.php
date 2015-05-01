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
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById($extensionId);
        return $ext->getConstant('BASE_WWW').'img/'.$path;
    }
    
    /**
     * Expects a relative url to the css as path
     * if extension name is omitted the current extension is used
     * 
     * @param string $path
     * @param string $extensionName
     * @return string
     */
    public static function css($path, $extensionName = null) {
        if (is_null($extensionName)) {
            $extensionName = \Context::getInstance()->getExtensionName();
        }
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById($extensionName);
        return $ext->getConstant('BASE_WWW').'css/'.$path;
    }
    
    /**
     * Expects a relative url to the java script as path
     * if extension name is omitted the current extension is used
     * 
     * @param string $path
     * @param string $extensionName
     * @return string
     */
    public static function js($path, $extensionName = null) {
        if (is_null($extensionName)) {
            $extensionName = \Context::getInstance()->getExtensionName();
        }
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById($extensionName);
        return $ext->getConstant('BASE_WWW').'js/'.$path;
    }
    
    /**
     * Expects a relative url to the template that is to be included as path
     * if extension name is omitted the current extension is used
     *
     * @param string $path
     * @param string $extensionName
     * @param array $data bind additional data to the context
     * @return string
     */
    public static function inc($path, $extensionName = null, $data = array()) {
        $context = \Context::getInstance();
        if (!is_null($extensionName) && $extensionName != $context->getExtensionName()) {
            // template is within different extension, change context
            $formerContext = $context->getExtensionName();
            $context->setExtensionName($extensionName);
        }

        if(count($data) > 0){
            \RenderContext::pushContext($data);
        }
        
        $absPath = self::getTemplate($path, $extensionName);
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
     * @param null $extensionName
     * @return string
     */
    public static function getTemplate($path, $extensionName = null) {
        $extensionName = is_null($extensionName) ? \Context::getInstance()->getExtensionName() : $extensionName;
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById($extensionName);
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
}
