<?php
use oat\oatbox\PhpSerializable;
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

/**
 *
 * Generis Object Oriented API - common/class.Utils.php
 *
 * Utility functions
 *
 * This file is part of Generis Object Oriented API.
 *
 * @author lionel.lecaque@tudor.lu
 * @package generis
 
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */

class common_Utils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Check if the given string is a proper uri
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string strarg
     * @return boolean
     */
    public static function isUri($strarg)
    {
        $returnValue = (bool) false;
        $uri = trim($strarg);
        if(!empty($uri)){
        	if( (preg_match("/^(http|https|file|ftp):\/\/[\/:.A-Za-z0-9_-]+#[A-Za-z0-9_-]+$/", $uri) && strpos($uri,'#')>0) || strpos($uri,"#")===0){
        		$returnValue = true;
        	}
        }
        return (bool) $returnValue;
    }
    
    public static function toResource($value) {
        if (is_array($value)) {
            $returnValue = array();
            foreach ($value as $val) {
                $returnValue[] = self::toResource($val);
            }
            return $returnValue;
        } else {
            if (common_Utils::isUri($value)) {
                return new core_kernel_classes_Resource($value);
            } else {
                return new core_kernel_classes_Literal($value);
            }
        }
    }

    /**
     * Removes starting/ending spaces, strip html tags out, remove any \r and \n
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string strarg
     * @return string
     */
    public static function fullTrim($strarg)
    {
        return strip_tags(trim($strarg));
    }


    /**
     * Short description of method getNewUri
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public static function getNewUri()
    {
        return core_kernel_uri_UriService::singleton()->generateUri();
    }

    /**
     * Returns the php code, that if evaluated
     * would return the value provided
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  value
     * @return string
     */
    public static function toPHPVariableString($value)
    {
		switch (gettype($value)) {
        	case "string" :
        	    $returnValue = '\''.str_replace('\'', '\\\'', str_replace('\\', '\\\\', $value)).'\'';
        		break;
        	case "boolean" :
        		$returnValue = $value ? 'true' : 'false';
        		break;
        	case "integer" :
        	case "double" :
        		$returnValue = $value;
        		break;
        	case "array" :
				$string = "";
				foreach ($value as $key => $val) {
					$string .= self::toPHPVariableString($key)." => ".self::toPHPVariableString($val).",";
				}
				$returnValue = "array(".substr($string, 0, -1).")";
				break;
        	case "NULL" :
        		$returnValue = 'null';
				break;
        	case "object" :
        	    if ($value instanceof PhpSerializable) {
        	       $returnValue = $value->__toPhpCode();
        	    } else {
        	       $returnValue =  'unserialize('.self::toPHPVariableString(serialize($value)).')';
        	    }
        		break;
        	default:
    			// resource and unexpected types
        		throw new common_exception_Error("Could not convert variable of type ".gettype($value)." to PHP variable string");
        }

        return (string) $returnValue;
    }
    
    /**
     * Same as toPHPVariableString except the sting
     * is easier for humans to read
     * 
     * @param mixed $value
     * @param number indentation
     * @return string
     */
    public static function toHumanReadablePhpString($value, $indentation = 0)
    {
        if (gettype($value) == "array") {
            $array = array_keys($value);
            $assocArray = ($array !== array_keys($array));
            $string = "";
            if ($assocArray) {
                foreach ($value as $key => $val) {
                	$string .= PHP_EOL.str_repeat('    ', $indentation+1)
                       .self::toPHPVariableString($key)
                	   ." => "
                       .self::toHumanReadablePhpString($val, $indentation+1).",";
                }
            } else {
                foreach ($value as $val) {
                    $string .= PHP_EOL.str_repeat('    ', $indentation+1)
                        .self::toHumanReadablePhpString($val, $indentation+1).",";
                }
            }
            $returnValue = "array(".substr($string, 0, -1).PHP_EOL.str_repeat('    ', $indentation).")";
        } else {
            $returnValue = self::toPHPVariableString($value);
        }
        
        return (string) $returnValue;
    }

}
