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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 */

/**
 * Short description of class taoQTI_models_classes_QTI_Object
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_Object extends taoQTI_models_classes_QTI_Element
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
            'taoQTI_models_classes_QTI_attribute_Data',
            'taoQTI_models_classes_QTI_attribute_Type',
            'taoQTI_models_classes_QTI_attribute_Width',
            'taoQTI_models_classes_QTI_attribute_Height'
        );
    }

    public function setAlt($object){
        if($object instanceof taoQTI_models_classes_QTI_Object || is_string($object)){
            $this->alt = $object;
        }
    }

    protected function getTemplateQtiVariables(){
        $variables = parent::getTemplateQtiVariables();
        if(!is_null($this->alt)){
            if($this->alt instanceof taoQTI_models_classes_QTI_Object){
                $variables['_alt'] = $this->alt->toQTI();
            }else{
                $variables['_alt'] = (string) $this->alt;
            }
        }
        return $variables;
    }

    public function toArray(){
        $data = parent::toArray();
        if(!is_null($this->alt)){
            if($this->alt instanceof taoQTI_models_classes_QTI_Object){
                $data['_alt'] = $this->alt->toArray();
            }else{
                $data['_alt'] = (string) $this->alt;
            }
        }
        return $data;
    }

}
/* end of class taoQTI_models_classes_QTI_Object */