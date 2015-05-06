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
     * if extension name is ommited the current extension is used
     * 
     * @param string $path
     * @param string $extenionName
     * @return string
     */
    public static function img($path, $extenionName = null) {
        if (is_null($extenionName)) {
            $extenionName = \Context::getInstance()->getExtensionName();
        }
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById($extenionName);
        return $ext->getConstant('BASE_WWW').'img/'.$path;
    }
    
    /**
     * Expects a relative url to the css as path
     * if extension name is ommited the current extension is used
     * 
     * @param string $path
     * @param string $extenionName
     * @return string
     */
    public static function css($path, $extenionName = null) {
        if (is_null($extenionName)) {
            $extenionName = \Context::getInstance()->getExtensionName();
        }
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById($extenionName);
        return $ext->getConstant('BASE_WWW').'css/'.$path;
    }
    
    /**
     * Expects a relative url to the java script as path
     * if extension name is ommited the current extension is used
     * 
     * @param string $path
     * @param string $extenionName
     * @return string
     */
    public static function js($path, $extenionName = null) {
        if (is_null($extenionName)) {
            $extenionName = \Context::getInstance()->getExtensionName();
        }
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById($extenionName);
        return $ext->getConstant('BASE_WWW').'js/'.$path;
    }
    
    /**
     * Expects a relative url to the template that is to be included as path
     * if extension name is ommited the current extension is used
     *
     * @param string $path
     * @param string $extenionName
     * @return string
     */
    public static function inc($path, $extenionName = null) {
        if (!is_null($extenionName) && $extenionName != \Context::getInstance()->getExtensionName()) {
            // template is within diffrent extension, change context
            $formerContext = \Context::getInstance()->getExtensionName();
            \Context::getInstance()->setExtensionName($extenionName);
        }
        
        $absPath = self::getTemplate($path, $extenionName);
        if (file_exists($absPath)) {
            include($absPath);
        } else {
            \common_Logger::w('Failed to include "'.$absPath.'" in template');
        }
        // restore context
        if (isset($formerContext)) {
            \Context::getInstance()->setExtensionName($formerContext);
        }
    }
    
    public static function getTemplate($path, $extenionName = null) {
        $extenionName = is_null($extenionName) ? \Context::getInstance()->getExtensionName() : $extenionName;
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById($extenionName);
        return $ext->getConstant('DIR_VIEWS').'templates'.DIRECTORY_SEPARATOR.$path;
    }
}
