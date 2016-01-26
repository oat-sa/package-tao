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

use oat\taoQtiItem\model\qti\interaction\HottextInteraction;
use oat\taoQtiItem\model\qti\interaction\ContainerInteraction;
use oat\taoQtiItem\model\qti\choice\Choice;
use oat\taoQtiItem\model\qti\exception\QtiModelException;

/**
 * QTI Hottext Interaction
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
class HottextInteraction extends ContainerInteraction
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'hottextInteraction';
    static protected $choiceClass = 'oat\\taoQtiItem\\model\\qti\\choice\\Hottext';
    static protected $containerType = 'oat\\taoQtiItem\\model\\qti\\container\\ContainerHottext';
    static protected $baseType = 'identifier';
    
    protected function getUsedAttributes(){
        return array_merge(
                parent::getUsedAttributes(), array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\MaxChoices',
            'oat\\taoQtiItem\\model\\qti\\attribute\\MinChoices'
                )
        );
    }

    public function getChoices($matchSet = null){
        return $this->getBody()->getElements(static::$choiceClass);
    }

    public function addChoice(Choice $choice, $matchSet = null){
        throw new QtiModelException('For Hottext Interaction, the choices are in the container, please use Container::setElement() instead');
    }

    public function createChoice($choiceAttributes = array(), $choiceValue = null, $matchSet = null){
        throw new QtiModelException('For Hottext Interaction, the choices are in the container, please use Container::setElement() instead');
    }

    public function removeChoice(Choice $choice, $matchSet = null){
        return $this->body->removeElement($choice);
    }

    protected function getTemplateQtiVariables(){
        $variables = parent::getTemplateQtiVariables();
        unset($variables['choices']); //hottexts are contained in the container already
        return $variables;
    }
    
    public function toArray($filterVariableContent = false, &$filtered = array()){
        $data = parent::toArray($filterVariableContent, $filtered);
        unset($data['choices']);
        return $data;
    }

}