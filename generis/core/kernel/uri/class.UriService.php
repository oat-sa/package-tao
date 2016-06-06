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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

/**
 * Iterates over a class(es) and its subclasses 
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class core_kernel_uri_UriService
{
    const CONFIG_KEY = 'uriProvider';
    	
    static private $instance; 
    
    static public function singleton()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private $uriProvider = null;
    
    /**
     * Generate a new URI with the UriProvider in force.
     * 
     * @return string
     */
    public function generateUri()
    {
        return (string) $this->getUriProvider()->provide();
        
    }
    
    /**
     * Set the UriProvider in force.
     * 
     * @param common_uri_UriProvider $provider
     */
    public function setUriProvider(common_uri_UriProvider $provider)
    {
        $this->uriProvider = $provider; 
        common_ext_ExtensionsManager::singleton()->getExtensionById('generis')->setConfig(self::CONFIG_KEY, $provider);
    }
    
    /**
     * Get the UriProvider in force.
     * 
     * @return common_uri_UriProvider
     */
    public function getUriProvider()
    {
        if (is_null($this->uriProvider)) {
            $this->uriProvider = common_ext_ExtensionsManager::singleton()->getExtensionById('generis')->getConfig(self::CONFIG_KEY);
        }
        return $this->uriProvider;
    }
}
