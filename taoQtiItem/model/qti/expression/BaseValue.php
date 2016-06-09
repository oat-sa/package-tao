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
namespace oat\taoQtiItem\model\qti\expression;

use oat\taoQtiItem\model\qti\expression\BaseValue;
use oat\taoQtiItem\model\qti\expression\CommonExpression;
use \common_Exception;

/**
 * Short description of class oat\taoQtiItem\model\qti\expression\BaseValue
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
class BaseValue
    extends CommonExpression
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRule()
    {
        $returnValue = (string) '';

        
         // JSON ENCODE the value to get quote when quote are required function of the variable base type
        // not so easy ;)
        //$returnValue = json_encode($this->value);
        // @todo make usable for complex variable such as pair, directed pair ..
        // @todo centralize the management of the options (attributes)
        $options = Array();
        $value = null;
        
        switch ($this->attributes['baseType']){
            case "boolean":
                $options['type'] = "boolean";
                $value = json_encode ($this->value);
                break;
            case "integer":
                $options['type'] = "integer";
                $value = json_encode ($this->value);
                break;
            case "float":
                $options['type'] = "float";
                $value = json_encode ($this->value);
                break;
            case "identifier":
            case "string":
                $options['type'] = "string";
                $value = json_encode ($this->value);
                break;
            case "pair":
                $options['type'] = "list";
                $value = '"'.implode ('","', $this->value).'"';
                break;
            case "directedPair":
                $options['type'] = "tuple";
                $value = '"'.implode ('","', (array)$this->value).'"'; // Méchant casting, won't work with a dictionnary, but with a tuple it is okay
                break;
            default:
                throw new common_Exception("taoQTI_models_classes_QTI_response_BaseValue::getRule an error occured : the type ".$this->attributes['baseType']." is unknown");
        }

        $returnValue = 'createVariable('
            . (count($options) ? '"'.addslashes(json_encode($options)).'"' : 'null') .
            ', '. $value .
        ')';
        

        return (string) $returnValue;
    }

}

?>