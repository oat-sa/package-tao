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
 */

class helpers_PropertyLgCacheHelper
{

    private static function getSerial($uri){
        return 'isPropertyLg' . md5($uri);
    }
    
    
    public static function getLgDependencyCache($uri){
           
        try {
            $lgDependencyCache = common_cache_FileCache::singleton()->get(self::getSerial($uri));
        }
        catch (common_cache_NotFoundException $e) {
            common_Logger::i('Could not find Lgdependent cache , initializing for ' . $uri);
            return null;
        }
        return $lgDependencyCache;
    }
    
    public static function setLgDependencyCache($uri,$bool){
   
        $lgDependencyCache = common_cache_FileCache::singleton()->put($bool, self::getSerial($uri));
    }
    
}

?>