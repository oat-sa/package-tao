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

use oat\taoQtiItem\model\qti\interaction\GapMatchInteraction;
use oat\taoQtiItem\model\qti\interaction\ContainerInteraction;
use oat\taoQtiItem\model\qti\choice\Gap;
use oat\taoQtiItem\model\qti\choice\Choice;

/**
 * QTI GapMatch Interaction
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
class GapMatchInteraction extends ContainerInteraction
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'gapMatchInteraction';
    static protected $choiceClass = 'oat\\taoQtiItem\\model\\qti\\choice\\GapText';
    static protected $baseType = 'directedPair';
    static protected $containerType = 'oat\\taoQtiItem\\model\\qti\\container\\ContainerGap';

    protected function getUsedAttributes(){
        return array_merge(
                parent::getUsedAttributes(), array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\Shuffle'
                )
        );
    }

    public function getGaps(){
        return $this->getBody()->getElements('oat\\taoQtiItem\\model\\qti\\choice\\Gap');
    }

    public function addGap($body, Gap $gap){
        return $this->setElements(array($gap), $body);
    }

    public function createGap($body, $gapAttributes = array(), $gapValue = null){
        $returnValue = null;
        $gap = new Gap($gapAttributes, $gapValue);
        if($this->addGap($body, $gap)){
            $returnValue = $gap;
        }
        return $returnValue;
    }

    public function removeGap(Gap $gap){
        return $this->body->removeElement($gap);
    }

    public function getIdentifiedElements(){
        $returnValue = parent::getIdentifiedElements();
        $returnValue->addMultiple($this->getGaps());
        return $returnValue;
    }
    
    public function getChoiceBySerial($serial){
        
        $returnValue = parent::getChoiceBySerial($serial);
        if(is_null($returnValue)){
            $gaps = $this->getGaps();
            if(isset($gaps[$serial])){
                $returnValue = $gaps[$serial];
            }
        }
        return $returnValue;
    }
    
    public function removeChoice(Choice $choice, $setNumber = null){
        if($choice instanceof Gap){
            return $this->body->removeElement($choice);
        }else{
            parent::removeChoice($choice);
        }
    }
    
}