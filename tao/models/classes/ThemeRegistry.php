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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\tao\model;

use oat\oatbox\AbstractRegistry;
use common_ext_ExtensionsManager;
use oat\tao\model\websource\WebsourceManager;
use Jig\Utils\FsUtils;


class ThemeRegistry extends AbstractRegistry
{

    const WEBSOURCE = 'websource_';
    /**
     *
     * @see \oat\oatbox\AbstractRegistry::getExtension()
     */
    protected function getExtension()
    {
        return common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
    }

    /**
     *
     * @see \oat\oatbox\AbstractRegistry::getConfigId()
     */
    protected function getConfigId()
    {
        return 'themes';
    }


    public function setWebSource($websource)
    {
        $this->set(ThemeRegistry::WEBSOURCE, $websource);
    }


    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $target
     * @param string $themeId
     * @throws \common_Exception
     */
    public function setDefaultTheme($target, $themeId)
    {
        $theme = $this->getTheme($target, $themeId);
        if(!is_null($theme)){
            $array = $this->get($target);
            $array['default'] = $themeId;
            $this->set($target, $array);
        }
    }
    
    /**
     * Get the theme array identified by its target and id
     * 
     * @param string $target
     * @param string $themeId
     * @return array
     * @throws \common_Exception
     */
    private function getTheme($target, $themeId){
        
        $returnValue = null;
        if(!$this->isRegistered($target)){
            throw new \common_Exception('Target '.$target.' does not exist');
        } else {
            $array = $this->get($target);
            $found = false;
            foreach ($array['available'] as $theme) {
                if ($theme['id'] == $themeId) {
                    $found = true;
                    $returnValue = $theme;
                    break;
                }
            }
            if (!$found) {
                throw new \common_Exception('Theme '.$themeId.' not found for target '.$target);
            }
        }
        return $returnValue;
    }
    
    /**
     * Get the default theme array
     */
    public function getDefaultTheme($target){
        $defaultTheme = null;
        if(!$this->isRegistered($target)){
            throw new \common_Exception('Target '.$target.' does not exist');
        } else {
            $array = $this->get($target);
            if(isset($array['default'])){
                $themeId = $array['default'];
                try{
                    $defaultTheme = $this->getTheme($target, $themeId);
                }catch(Exception $e){
                    //not found
                    $defaultTheme = null;
                }
            }
        }
        return $defaultTheme;
    }
    
    /**
     * Adds a new target to the System
     *
     * @param string $targetId
     * @param string|array $base
     * @throws \common_Exception
     */
    public function createTarget($targetId, $base)
    {

        if(!is_string($base) && !is_array($base)){
            throw new \common_Exception('Invalid base format');
        }

        $array = array(
            'base'  => $base,
            'available' => array()
        );
        $this->set($targetId, $array);
    }


    /**
     * Adds a theme to the registry
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $id
     * @param string $name
     * @param string $path
     * @param array $targets
     * @throws \common_Exception
     */
    public function registerTheme($id, $name, $path = '', $targets = array(), $templates = array() )
    {
        if (preg_match('/^[a-zA-Z0-9]*$/', $id) === 0) {
            throw new \common_Exception('Invalid id "'.$id.'"');
        }
        if (!is_array($targets) || count($targets) === 0){
            throw new \common_Exception('No targets were provided for theme '.$id);
        }

        foreach ($targets as $target) {
            if(!$this->isRegistered($target)){
                throw new \common_Exception('Target '.$target.' does not exist');
            } else {
                
                $array = $this->get($target);
                
                foreach ($array['available'] as $theme) {
                    if ($theme['id'] == $id) {
                        throw new \common_Exception('Theme '.$id.' already exists for target '.$target);
                    }
                }
                
                $theme = array(
                    'id' => $id,
                    'name' => $name
                );
                
                //the path is optional
                if($path){
                    $theme['path'] = $path;
                }
                
                //register templates
                if(is_array($templates) && count($templates) > 0){
                    $theme['templates'] = array();
                    foreach($templates as $templateId => $tpl){
                        $theme['templates'][$templateId] = $tpl;
                    }
                }
                
                $array['available'][] = $theme;
            }
            $this->set($target, $array);
        }
    }

    /**
     *
     * @author Joel Bout, joel@taotesting.com
     *
     * @param string $id
     * @throws \common_Exception
     */
    public function unregisterTheme($id)
    {
        if (preg_match('/^[a-zA-Z0-9]*$/', $id) === 0) {
            throw new \common_Exception('Invalid id "'.$id.'"');
        }

        $isDeleted = false;

        $map = $this->getMap();
        unset($map[ThemeRegistry::WEBSOURCE]);//still ugly but looks better than 'continue'
        foreach ($map as $target => $themes) {
            foreach ($themes['available'] as $key => $theme) {
                if ($theme['id'] == $id) {
                    unset($themes['available'][$key]);
                    $isDeleted = true;
                }
            }
            $this->set($target, $themes);
        }

        if ( !$isDeleted ){
            throw new \common_Exception('Theme '.$id.' not found for any target');
        }
    }

    /**
     * @param $theme
     * @return mixed
     */
    private function updatePath($theme){
        
        if(isset($theme['path'])){
            if(strpos($theme['path'] , ThemeRegistry::WEBSOURCE) === 0) {
                $websource = WebsourceManager::singleton()->getWebsource($this->get(ThemeRegistry::WEBSOURCE));
                $webUrl = $websource->getAccessUrl(substr($theme['path'],strlen(ThemeRegistry::WEBSOURCE)));
                $theme['path'] = $webUrl;
            }
            else {
                // normalizing makes sure that whatever\\comes/in gets/out/properly
                $theme['path'] = ROOT_URL . FsUtils::normalizePath($theme['path']) ;

            }
        }
        
        return $theme;
    }

    /**
     * Get the resolved absolute URL for a stylesheet
     * 
     * @param string $path
     * @return string
     */
    private function resolveStylesheetUrl($path){
        $websource = WebsourceManager::singleton()->getWebsource($this->get(ThemeRegistry::WEBSOURCE));
        if(strpos($path , ThemeRegistry::WEBSOURCE) === 0) {
                return $websource->getAccessUrl(substr($path, strlen(ThemeRegistry::WEBSOURCE)));
        }
        else {
            // normalizing makes sure that whatever\\comes/in gets/out/properly
            return ROOT_URL . FsUtils::normalizePath($path);
        }
    }
    
    /**
     * Resolve the template absolute path
     * 
     * @todo make it support templates as data
     * @param string $tpl
     * @return string
     */
    private function resolveTemplatePath($tpl){
        return ROOT_PATH.$tpl;
    }

    /**
     * Resolve the path and url defined in target "base"
     * 
     * @param string $target
     * @return mixed
     * @throws common_Exception
     */
    private function getResolvedBase($target){

        $base = null;
        $array = $this->get($target);
        
        if(is_string($array['base'])){

            $base = ROOT_URL . $array['base'];

        }else if(is_array($array['base'])){

            $base = array(
                'css' => $this->resolveStylesheetUrl($array['base']['css']),
                'templates' => array()
            );

            foreach($array['base']['templates'] as $id => $path){
                $base['templates'][$id] = $this->resolveTemplatePath($path);
            }

        }else{
            throw new common_Exception('invalid type for theme base');
        }

        return $base;
    }
    
    /**
     * Get list of available theme
     * The available themes have their URL and paths resolved
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function getAvailableThemes()
    {

        $returnValue = array();
        foreach ($this->getMap() as $target => $value) {
            //ugly
            if($target == ThemeRegistry::WEBSOURCE) {
                continue;
            }
            //retrieve all other value
            $returnValue[$target] = $value;

            // adapt path for all theme
            $returnValue[$target]['available'] = array();
            foreach ($value['available'] as $theme) {
                $returnValue[$target]['available'][] = $this->updatePath($theme);
            }

            $returnValue[$target]['base'] = $this->getResolvedBase($target);
        }
        return $returnValue;
    }

    /**
     * Get the absolute path to a theme template
     * 
     * @deprecated use theme\ThemeService instead
     * @param string $target
     * @param string $themeId
     * @param string $templateId
     * @return string
     */
    public function getTemplate($target, $themeId, $templateId){
        $theme = $this->getTheme($target, $themeId);
        if(isset($theme['templates']) && isset($theme['templates'][$templateId])){
            return $this->resolveTemplatePath($theme['templates'][$templateId]);
        }
        return null;
    }

    /**
     * Get the abosolute url to a stylesheet
     * 
     * @deprecated use theme\ThemeService instead
     * @param string $target
     * @param string $themeId
     * @return string
     */
    public function getStylesheet($target, $themeId){
        $theme = $this->getTheme($target, $themeId);
        if(isset($theme['path'])){
            return $this->resolveStylesheetUrl($theme['path']);
        }
        return null;//not found
    }

    /**
     * Get the asbolute path to the base template
     *
     * @deprecated use theme\ThemeService instead
     * @param string $target
     * @param string $templateId
     * @return string
     */
    public function getBaseTemplate($target, $templateId){
        $base = $this->getResolvedBase($target);
        if(is_array($base) && isset($base['templates']) && isset($base['templates'][$templateId])){
            return $base['templates'][$templateId];
        }
        return null;
    }

    /**
     * Get the absolute url to the base css
     * 
     * @deprecated use theme\ThemeService instead
     * @param string $target
     * @return string
     */
    public function getBaseStylesheet($target){
        $base = $this->getResolvedBase($target);
        if(is_string($base)){
            return $base;
        }else if(is_array($base) && isset($base['css'])){
            return $base['css'];
        }
        return null;
    }
}
