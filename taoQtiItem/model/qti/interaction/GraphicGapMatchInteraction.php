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

use oat\taoQtiItem\model\qti\interaction\GraphicGapMatchInteraction;
use oat\taoQtiItem\model\qti\interaction\GraphicInteraction;
use oat\taoQtiItem\model\qti\choice\GapImg;
use oat\taoQtiItem\model\qti\Object;
use oat\taoQtiItem\model\qti\choice\Choice;

/**
 * QTI Graphic Associate Interaction
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10373
 
 */
class GraphicGapMatchInteraction extends GraphicInteraction
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'graphicGapMatchInteraction';
    static protected $choiceClass = 'oat\\taoQtiItem\\model\\qti\\choice\\AssociableHotspot';
    static protected $baseType = 'directedPair';
    protected $gapImgs = array();

    /**
     * 
     * @return oat\taoQtiItem\model\qti\choice\Choice
     */
    public function createGapImg($objectLabel = '', $objectAttributes = array()){

        $returnValue = null;

        if(!empty(static::$choiceClass) && is_subclass_of(static::$choiceClass, 'oat\\taoQtiItem\\model\\qti\\choice\\Choice')){
            $returnValue = new GapImg(empty($objectLabel) ? array() : array('objectLabel' => (string) $objectLabel));
            $returnValue->setContent(new Object($objectAttributes));
            $this->addGapImg($returnValue);
        }

        return $returnValue;
    }

    public function addGapImg(GapImg $gapImg){
        $this->gapImgs[$gapImg->getSerial()] = $gapImg;
        $relatedItem = $this->getRelatedItem();
        if(!is_null($relatedItem)){
            $gapImg->setRelatedItem($relatedItem);
        }
    }

    public function getGapImgs(){
        return $this->gapImgs;
    }

    public function getIdentifiedElements(){
        $returnValue = parent::getIdentifiedElements();
        $returnValue->addMultiple($this->getGapImgs());
        return $returnValue;
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){
        $data = parent::toArray($filterVariableContent, $filtered);
        $data['gapImgs'] = $this->getArraySerializedElementCollection($this->getGapImgs(), $filterVariableContent, $filtered);
        return $data;
    }

    public static function getTemplateQti(){
        return static::getTemplatePath().'interactions/qti.graphicGapMatchInteraction.tpl.php';
    }

    protected function getTemplateQtiVariables(){
        $variables = parent::getTemplateQtiVariables();
        $variables['gapImgs'] = '';
        foreach($this->getGapImgs() as $gapImg){
            $variables['gapImgs'] .= $gapImg->toQTI();
        }
        return $variables;
    }
    
    public function getChoiceBySerial($serial){
        
        $returnValue = parent::getChoiceBySerial($serial);
        if(is_null($returnValue)){
            $gapImgs = $this->getGapImgs();
            if(isset($gapImgs[$serial])){
                $returnValue = $gapImgs[$serial];
            }
        }
        return $returnValue;
    }
    
    public function removeChoice(Choice $choice, $setNumber = null){
        if($choice instanceof GapImg){
            unset($this->gapImgs[$choice->getSerial()]);
        }else{
            parent::removeChoice($choice);
        }
    }
    
}
