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
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */
namespace qtism\common\utils;

/**
 * A utility class focusing on URLs (Uniform Resource Locator).
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Url {
    
    /**
     * Remove leading and trailing slashes (/) and whitespaces (\t, \n, \r, \0, \x0B)  from a given URL
     * or $urlComponent.
     * 
     * @return The trimmed URL or URL component.
     */
    static public function trim($urlComponent) {
        // Trim is UTF-8 safe if the second argument does not
        // contain multi-byte chars.
        return trim($urlComponent, "/\t\n\r\0\x0B");
    }
    
    /**
     * Remove leading slashes (/) and whitespaces (\t, \n, \r, \0, \x0B)  from a given URL
     * or $urlComponent.
     *
     * @return The trimmed URL or URL component.
     */
    static public function ltrim($urlComponent) {
        return ltrim($urlComponent, "/\t\n\r\0\x0B");
    }
    
    /**
     * Remove trailing slashes (/) and whitespaces (\t, \n, \r, \0, \x0B)  from a given URL
     * or $urlComponent.
     *
     * @return The trimmed URL or URL component.
     */
    static public function rtrim($urlComponent) {
        return rtrim($urlComponent, "/\t\n\r\0\x0B");
    }
    
    /**
     * Whether or not a given $url is relative.
     * 
     * @param string $url
     * @return boolean
     */
    static public function isRelative($url) {
        return (preg_match("/^[a-z][a-z0-9+\-\.]+:(\/\/){0,1}|^\//i", $url) === 0) ? true : false;
    }
}