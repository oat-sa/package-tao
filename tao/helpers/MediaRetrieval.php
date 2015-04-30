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



class MediaRetrieval{


    /**
     * get the identifier (if one) and the relPath of a media from a path
     *
     * @param $path
     * @return array ['identifier' => xxx, 'relPath' => yyy]
     */
    public static function getRealPathAndIdentifier($path){
        //remove / at the beginning of the string
        $relPath = ltrim($path, '/');

        //get the identifier of the media
        $identifier = substr($relPath, 0, strpos($relPath, '/'));
        if(self::isIdentifierValid($identifier)){
            $relPath = substr($relPath, strpos($relPath, '/') + 1);
        }

        return compact('identifier', 'relPath');

    }

    /**
     * Check if the identifier exists in the config
     * @param $identifier
     * @return bool
     */
    public static function isIdentifierValid($identifier){

        $tao = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        if($identifier === 'local'){
            return true;
        }
        if ($tao->hasConfig('mediaBrowserSources')) {
            $configs = $tao->getConfig('mediaBrowserSources');
            if (isset($configs[$identifier])) {
                return true;
            }
        }


        return false;
    }
}
