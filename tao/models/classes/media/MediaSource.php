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
 *
 */

namespace oat\tao\model\media;

class MediaSource{

    const CONFIG_BROWSER_KEY = 'mediaBrowserSources';
    const CONFIG_MANAGEMENT_KEY = 'mediaManagementSources';

    /**
     * @var array
     */
    private static $mediaBrowserSources = array();
    private static $mediaManagementSources = array();


    public static function getMediaBrowserSources(){
        $tao = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $configs = $tao->hasConfig(self::CONFIG_BROWSER_KEY)
            ? $tao->getConfig(self::CONFIG_BROWSER_KEY)
            : array();

        foreach($configs as $mediaSourceId => $mediaSource){
            self::getMediaBrowserSource($mediaSourceId);
        }

        return self::$mediaBrowserSources;
    }

    public static function getMediaManagementSources(){
        $tao = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $configs = $tao->hasConfig(self::CONFIG_MANAGEMENT_KEY)
            ? $tao->getConfig(self::CONFIG_MANAGEMENT_KEY)
            : array();

        foreach($configs as $mediaSourceId => $mediaSource){
            self::getMediaManagementSource($mediaSourceId);
        }

        return self::$mediaManagementSources;
    }

    /**
     *
     * @param string $mediaSourceId
     * @return MediaBrowser
     */
    public static function getMediaBrowserSource($mediaSourceId){
        if(!isset(self::$mediaBrowserSources[$mediaSourceId])) {
            self::$mediaBrowserSources[$mediaSourceId] = self::createMediaSource($mediaSourceId, 'browser');
        }
        return self::$mediaBrowserSources[$mediaSourceId];
    }

    public static function getMediaManagementSource($mediaSourceId){
        if(!isset(self::$mediaManagementSources[$mediaSourceId])) {
            self::$mediaManagementSources[$mediaSourceId] = self::createMediaSource($mediaSourceId, 'management');
        }
        return self::$mediaManagementSources[$mediaSourceId];
    }

    /**
     * Add a new persistence to the system
     *
     * @param string $mediaSourceId
     * @param string $mediaSource
     * @param string $type
     */
    public static function addMediaSource($mediaSourceId, $mediaSource, $type = 'browser') {
        switch($type){
            case 'browser':
                $key = self::CONFIG_BROWSER_KEY;
                break;
            case 'management':
                $key = self::CONFIG_MANAGEMENT_KEY;
                break;
            default :
                $key = self::CONFIG_BROWSER_KEY;
                break;
        }

        $tao = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $configs = $tao->hasConfig($key)
            ? $tao->getConfig($key)
            : array();
        $configs[$mediaSourceId] = $mediaSource;
        $tao->setConfig($key, $configs);
    }

    /**
     * Add a new persistence to the system
     *
     * @param string $mediaSourceId
     * @param string $type
     */
    public static function removeMediaSource($mediaSourceId, $type = 'browser') {
        switch($type){
            case 'browser':
                $key = self::CONFIG_BROWSER_KEY;
                $sources = self::$mediaBrowserSources;
                break;
            case 'management':
                $key = self::CONFIG_MANAGEMENT_KEY;
                $sources = self::$mediaManagementSources;
                break;
            default :
                $key = self::CONFIG_BROWSER_KEY;
                $sources = self::$mediaBrowserSources;
                break;
        }


        $tao = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $configs = $tao->hasConfig($key)
            ? $tao->getConfig($key)
            : array();
        if(isset($configs[$mediaSourceId])){
            unset($configs[$mediaSourceId]);
            if(isset($sources[$mediaSourceId])){
                unset($sources[$mediaSourceId]);
            }
            $tao->setConfig($key, $configs);
        }
        else{
            throw new \common_Exception('Media Sources Configuration for source '.$mediaSourceId.' not found');
        }

    }

    /**
     * @param string $mediaSourceId
     * @param string $type
     * @throws \common_Exception
     * @return MediaBrowser
     */
    private static function createMediaSource($mediaSourceId, $type = 'browser') {
        switch($type){
            case 'browser':
                $key = self::CONFIG_BROWSER_KEY;
                break;
            case 'management':
                $key = self::CONFIG_MANAGEMENT_KEY;
                break;
            default :
                $key = self::CONFIG_BROWSER_KEY;
                break;
        }

        $tao = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        if ($tao->hasConfig($key)) {
            $configs = $tao->getConfig($key);
            if (isset($configs[$mediaSourceId])) {
                $config = $configs[$mediaSourceId];
                return $config;
            } else {
                throw new \common_Exception('Media Sources Configuration for source '.$mediaSourceId.' not found');
            }
        } else {
            throw new \common_Exception('Media Sources Configuration not found');
        }
    }

} 