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

namespace oat\taoOutcomeUi\helper;

/**
 * A class focusing on providing utility methods for the various result datatypes
 * that might be sent/stored to/by a result server.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Datatypes {
    
    /**
     * Decode a binary string representing a file into an array.
     * 
     * The binary string that is decoded must respect the following scheme:
     * 
     * * filename length, unsigned short
     * * filename, string
     * * mimetype length, unsigned short
     * * mimetype, string
     * * binary content of the file, string
     * 
     * The returned array contains tree cells with the following keys:
     * 
     * * name, string (might be empty)
     * * mime, string
     * * data, string
     * 
     * @param string $binary A binary string representing the file to be decoded.
     * @return array The decoded file as an array.
     */
    static public function decodeFile($binary) {
        
        $returnValue = array('name' => '', 'mime' => '', 'data' => '');
        
        if (empty($binary) === false) {
            
            $filenameLen = current(unpack('S', substr($binary, 0, 2)));
            if ($filenameLen > 0) {
                $returnValue['name'] = substr($binary, 2, $filenameLen);
            }
            
            $mimetypeLen = current(unpack('S', substr($binary, 2 + $filenameLen, 2)));
            $returnValue['mime'] = substr($binary, 4 + $filenameLen, $mimetypeLen);
            
            $returnValue['data'] = substr($binary, 4 + $filenameLen + $mimetypeLen);
        }
        
        return $returnValue;
    }
}