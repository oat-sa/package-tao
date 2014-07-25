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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti\datatype;

use oat\taoQtiItem\model\qti\datatype\View;
use oat\taoQtiItem\model\qti\datatype\Datatype;

/**
 * The basic View data type
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
class View extends Datatype
{

    public static function validate($value){
        if(is_array($value) && count($value)){//cardinality 1..*
            $enum = static::getEnumeration();
            foreach($value as $val){
                if(!in_array($val, $enum)){
                    return false;
                }
            }
        }else{
            return false;
        }
        return true;
    }

    public static function fix($value){

        $returnValue = null;

        if(is_string($value)){//load the multiple attribute value like: "proctor tutor scorer author testConstructor"
            $value = explode(' ', $value);
        }

        if(is_array($value) && count($value)){//cardinality 1..*
            $enum = static::getEnumeration();
            foreach($value as $val){
                foreach($enum as $element){
                    if(strcasecmp($val, $element) == 0){//case insensitive comparison and element selection
                        if(is_null($returnValue)){
                            $returnValue = array();
                        }
                        $returnValue[] = $element;
                        break;
                    }
                }
            }
        }

        return $returnValue;
    }

    public static function getEnumeration(){
        return array(
            'author',
            'candidate',
            'proctor',
            'scorer',
            'testConstructor',
            'tutor'
        );
    }

}