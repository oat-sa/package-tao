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
 */

namespace oat\taoQtiItem\model\qti;

use oat\taoQtiItem\model\qti\Object;
use oat\taoQtiItem\model\qti\Element;

/**
 * Short description of class oat\taoQtiItem\model\qti\Object
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
class Object extends Element
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'object';

    /**
     * The alternate object to be displayed, can be a nested object or some text/html
     *  
     * @var mixed
     */
    protected $alt = null;

    public function getUsedAttributes(){
        return array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\Data',
            'oat\\taoQtiItem\\model\\qti\\attribute\\Type',
            'oat\\taoQtiItem\\model\\qti\\attribute\\Width',
            'oat\\taoQtiItem\\model\\qti\\attribute\\Height'
        );
    }

    public function setAlt($object){
        if($object instanceof Object || is_string($object)){
            $this->alt = $object;
        }
    }

    protected function getTemplateQtiVariables(){
        $variables = parent::getTemplateQtiVariables();
        if(!is_null($this->alt)){
            if($this->alt instanceof Object){
                $variables['_alt'] = $this->alt->toQTI();
            }else{
                $variables['_alt'] = (string) $this->alt;
            }
        }
        return $variables;
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){
        $data = parent::toArray($filterVariableContent, $filtered);
        if(!is_null($this->alt)){
            if($this->alt instanceof Object){
                $data['_alt'] = $this->alt->toArray($filterVariableContent, $filtered);
            }else{
                $data['_alt'] = (string) $this->alt;
            }
        }
        return $data;
    }

}