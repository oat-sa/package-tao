<?php
/*
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * DEPRECATED, use json_encode instead. Preferably not into the template but in the controller.
 * 
 * A helper to fascilitate the exchange of data between php and javascript
 * 
 * @deprecated since version 2.6
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_helpers_Javascript
{
    /**
     * converts a php array or string into a javascript format
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  mixed $var
     * @param  boolean format
     * @return string
     */
    public static function buildObject($var, $format = false){
        if (is_array($var)) {
            $returnValue = '{';
            $i = 1;
            foreach($var as $k => $v){
                $k = is_int($k)?'"'.json_encode($k).'"':json_encode($k);
                $returnValue .= $k.':'.self::buildObject($v, $format);
                $returnValue .= ($i < count($var)) ? ',' : '';
                $returnValue .= $format ? PHP_EOL : '';
                $i++;
            }
            $returnValue .= '}';
        } else {
            
            // Some versions of PHP simply fail
            // when encoding non UTF-8 data. Some other
            // versions return null... If it fails, simply
            // reproduce a single failure scenario.
            $returnValue = @json_encode($var);
            
            if ($returnValue === false) {
                $returnValue = json_encode(null);
            }
        }
        
        return $returnValue;
    }
}