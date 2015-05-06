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

namespace oat\taoQtiItem\model\qti\interaction;

use oat\taoQtiItem\model\qti\interaction\Interaction;

/**
 * The QTI custom interaction is a subclass of the main QTI Interaction class
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10267

 */
class CustomInteraction extends Interaction
{
    
    protected static $qtiTagName = 'customInteraction';
    
    protected $typeIdentifier = '';
    protected $properties = array();
    protected $libraries = array();
    protected $markup = '';
    
    public function setTypeIdentifier($typeIdentifier){
        $this->typeIdentifier = $typeIdentifier;
    }
    
    public function getTypeIdentifier($typeIdentifier){
        return $this->typeIdentifier;
    }
    
    public function getMarkup(){
        return $this->markup;
    }

    public function setMarkup($markup){
        $this->markup = (string) $markup;
    }

    public function getProperties(){
        return $this->properties;
    }

    public function setProperties($properties){
        if(is_array($properties)){
            $this->properties = $properties;
        }else{
            throw new InvalidArgumentException('properties should be an array');
        }
    }

    public function getLibraries(){
        return $this->libraries;
    }

    public function setLibraries($libraries){
        if(is_array($libraries)){
            $this->libraries = $libraries;
        }else{
            throw new InvalidArgumentException('libraries should be an array');
        }
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){
        
        $returnValue = parent::toArray($filterVariableContent, $filtered);
        
        $returnValue['typeIdentifier'] = $this->typeIdentifier;
        $returnValue['markup'] = $this->markup;
        $returnValue['libraries'] = $this->libraries;
        $returnValue['properties'] = $this->properties;
        
        return $returnValue;
    }

    public static function getTemplateQti(){
        return static::getTemplatePath().'interactions/qti.customInteraction.tpl.php';
    }

    protected function getTemplateQtiVariables(){
        $variables = parent::getTemplateQtiVariables();
        $variables['markup'] = $this->markup;
        $variables['properties'] = $this->properties;
        return $variables;
    }

}