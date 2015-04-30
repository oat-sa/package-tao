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

use oat\taoThemingPlatform\model\PlatformThemingService;

use oat\tao\helpers\Template;
use oat\tao\model\menu\Icon;
use \common_ext_ExtensionsManager;
use \tao_helpers_Scriptloader;

class Layout{


    /**
     * Compute the parameters for the release message
     *
     * @return array
     */
    public static function getReleaseMsgData(){
        $params = array(
            'version-type' => '',
            'is-unstable'  => self::isUnstable(),
            'is-sandbox'   => false,
            'logo'         => self::getLogoUrl(),
            'link'         => self::getLinkUrl(),
            'msg'          => self::getMessage()
        );

        switch(TAO_RELEASE_STATUS){
            case 'alpha':
            case 'demoA':
                $params['version-type'] = __('Alpha version');
                break;

            case 'beta':
            case 'demoB':
                $params['version-type'] = __('Beta version');
                break;

            case 'demoS':
                $params['version-type'] = __('Demo Sandbox');
                $params['is-sandbox']    = true;
                break;
        }

        return $params;
    }


    /**
     * Compute the expiration time for the sandbox version
     *
     * @return string
     */
    public static function getSandboxExpiration(){
        $datetime   = new \DateTime();
        $d          = new \DateTime($datetime->format('Y-m-d'));
        $weekday    = $d->format('w');
        $weekNumber = $d->format('W');
        $diff       = $weekNumber % 2 ? 7 : 6 - $weekday;
        $d->modify(sprintf('+ %d day', $diff));
        return \tao_helpers_Date::displayInterval($d, \tao_helpers_Date::FORMAT_INTERVAL_LONG);
    }

    /**
     * $icon defined in oat\tao\model\menu\Perspective::fromSimpleXMLElement
     *
     * $icon has two methods, getSource() and getId().
     * There are three possible ways to include icons, either as font, img or svg (not yet supported).
     * - Font uses source to address the style sheet (TAO font as default) and id to build the class name
     * - Img uses source only
     * - Svg uses source to address an SVG sprite and id to point to the right icon in there
     *
     * @param Icon $icon
     * @param string $defaultIcon e.g. icon-extension | icon-action
     * @return string icon as html
     */
    public static function renderIcon($icon, $defaultIcon) {

        $srcExt   = '';
        $isBase64 = false;
		$iconClass = $defaultIcon;
		if(!is_null($icon)){

            if($icon -> getSource()) {
                $imgXts   = 'png|jpg|jpe|jpeg|gif';
                $regExp   = sprintf('~((^data:image/(%s))|(\.(%s)$))~', $imgXts, $imgXts);
                $srcExt   = preg_match($regExp, $icon -> getSource(), $matches) ? array_pop($matches) : array();
                $isBase64 = 0 === strpos($icon -> getSource(), 'data:image');
            }

            $iconClass = $icon -> getId() ? $icon -> getId() : $defaultIcon;
        }
        // clarification icon vs. glyph: same thing but due to certain CSS rules a second class is required

        switch($srcExt) {
            case 'png':
            case 'jpg':
            case 'jpe':
            case 'jpeg':
            case 'gif':
                return $isBase64
                    ? '<img src="' . $icon -> getSource() . '" alt="" class="glyph" />'
                    : '<img src="' . Template::img($icon -> getSource(), $icon -> getExtension()) . '" alt="" class="glyph" />';
                break;

            case 'svg':
                // not implemented yet
                return false;

            case ''; // no source means an icon font is used
                return sprintf('<span class="%s glyph"></span>', $iconClass);
        }
    }

    /**
     * Build script element for AMD loader
     *
     * @return string
     */
    public static function getAmdLoader(){
        if(\common_session_SessionManager::isAnonymous()) {
            $amdLoader = array(
                'src' => Template::js('lib/require.js', 'tao'),
                //'data-main' => TAOBASE_WWW . 'js/main'
                'data-main' => TAOBASE_WWW . 'js/login',
                'data-config' => get_data('client_config_url')
            );
        }
        else if(\tao_helpers_Mode::is('production')) {
            $amdLoader = array(
                'src' => Template::js('main.min.js', 'tao'),
                'data-config' => get_data('client_config_url')
            );
        }
        else {
            $amdLoader = array(
                'src' => Template::js('lib/require.js', 'tao'),
                'data-config' => get_data('client_config_url'),
                'data-main' => TAOBASE_WWW . 'js/main'
            );
        }

        $amdScript = '<script id="amd-loader" ';
        foreach($amdLoader as $attr => $value) {
            $amdScript .= $attr . '="' . $value . '" ';
        }
        return trim($amdScript) . '></script>';
    }

    /**
     * @return string
     */
    public static function getTitle() {
        $title = get_data('title');
        return $title ? $title : PRODUCT_NAME . ' ' .  TAO_VERSION;
    }


    /**
     * Retrieve the template with the actual content
     *
     * @return array
     */
    public static function getContentTemplate() {
        $templateData = (array)get_data('content-template');
        $contentTemplate['path'] = $templateData[0];
        $contentTemplate['ext']  = $templateData[1] ? $templateData[1] : 'tao';
        return $contentTemplate;
    }

    private static function isThemingEnabled() {
        $extManager = \common_ext_ExtensionsManager::singleton();
        return $extManager->isInstalled('taoThemingPlatform') && $extManager->isEnabled('taoThemingPlatform');
    }
    
    /**
     * Get the logo URL.
     * 
     * @return string The absolute URL to the logo image.
     */
    public static function getLogoUrl() {
        $logoFile = Template::img('tao-logo.png', 'tao');

        if (self::isThemingEnabled() === true) {
            // Get Theming info from taoThemingPlatform...
            $themingService = PlatformThemingService::singleton();
            $themingConfig = $themingService->retrieveThemingConfig();
            if ($themingConfig['logo'] !== null) {
                $logoFile = $themingService->getFileUrl($themingConfig['logo']);
            }
            
        } else {
            switch (TAO_RELEASE_STATUS) {
                case 'alpha':
                case 'demoA':
                    $logoFile = Template::img('tao-logo-alpha.png', 'tao');
                    break;
                    
                case 'beta':
                case 'demoB':
                    $logoFile = Template::img('tao-logo-beta.png', 'tao');
                    break;
            }
        }
        
        return $logoFile;
    }
    
    public static function getThemeUrl() {
        if (self::isThemingEnabled() === true) {
            $themingService = PlatformThemingService::singleton();
            if ($themingService->hasFile('platformtheme.css')) {
                return $themingService->getFileUrl('platformtheme.css');
            }
        }
    }

    public static function getLinkUrl() {
        $link = 'http://taotesting.com';

        if (self::isThemingEnabled() === true) {
            // Get Theming info from taoThemingPlatform...
            $themingService = PlatformThemingService::singleton();
            $themingConfig = $themingService->retrieveThemingConfig();
            if ($themingConfig['link'] !== null) {
                $link = $themingConfig['link'];
            }

        } else {
            switch (TAO_RELEASE_STATUS) {
                case 'alpha':
                case 'demoA':
                case 'beta':
                case 'demoB':
                    $link = 'http://forge.taotesting.com/projects/tao';
                    break;
            }
        }

        return $link;
    }

    public static function getMessage() {
        $message = __('Tao Home');

        if (self::isThemingEnabled() === true) {
            // Get Theming info from taoThemingPlatform...
            $themingService = PlatformThemingService::singleton();
            $themingConfig = $themingService->retrieveThemingConfig();
            if (empty($themingConfig['message']) === false) {
                $message = $themingConfig['message'];
            }
        } else {
            switch (TAO_RELEASE_STATUS) {
                case 'alpha':
                case 'demoA':
                case 'beta':
                case 'demoB':
                    $message = __('Please report bugs, ideas, comments or feedback on the TAO Forge');
                    break;
            }
        }

        return $message;
    }
    
    public static function isUnstable() {
        $isUnstable = true;
        
        if (self::isThemingEnabled() === true) {
            $themingService = PlatformThemingService::singleton();
            $themingConfig = $themingService->retrieveThemingConfig();
            
            if (empty($themingConfig['stable']) === false) {
                $isUnstable = !$themingConfig['stable'];
            }
        } else {
            switch (TAO_RELEASE_STATUS) {
                case 'demoS':
                case 'stable':
                    $isUnstable = false;
                    break;
            }
        }
        
        return $isUnstable;
    }
    
    public static function getLoginMessage() {
        
        $message = __("Connect to the TAO platform");
        
        if (self::isThemingEnabled() === true) {
            $themingService = PlatformThemingService::singleton();
            $themingConfig = $themingService->retrieveThemingConfig();
        
            if (empty($themingConfig['login_message']) === false) {
                $message = $themingConfig['login_message'];
            }
        }
        
        return $message;
    }
    
    public static function getLoginLabel() {
        $loginLabel = __("Login");
        
        if (self::isThemingEnabled() === true) {
            $themingService = PlatformThemingService::singleton();
            $themingConfig = $themingService->retrieveThemingConfig();
        
            if (empty($themingConfig['login_field']) === false) {
                $loginLabel = $themingConfig['login_field'];
            }
        }
        
        return $loginLabel;
    }
    
    public static function getPasswordLabel() {
        $passwordLabel = __("Password");
    
        if (self::isThemingEnabled() === true) {
            $themingService = PlatformThemingService::singleton();
            $themingConfig = $themingService->retrieveThemingConfig();
    
            if (empty($themingConfig['password_field']) === false) {
                $passwordLabel = $themingConfig['password_field'];
            }
        }
    
        return $passwordLabel;
    }
    
    public static function getCopyrightNotice() {
        $copyrightNotice = '';
        
        if (self::isThemingEnabled() === true) {
            $themingService = PlatformThemingService::singleton();
            $themingConfig = $themingService->retrieveThemingConfig();
    
            if (empty($themingConfig['copyright_notice']) === false) {
                $copyrightNotice = $themingConfig['copyright_notice'];
            }
        }
        
        return $copyrightNotice;
    }
}
