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
 * 
 */

/**
 * The QTI_Feedback object represent one of the three available feedbackElements
 * (feedbackInline, feedbackBlock, feedbackModal
 *
 * @access public
 * @author Sam Sipasseuth, <sam.sipasseuth@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10243
 * @subpackage models_classes_QTI
 */
abstract class taoQTI_models_classes_QTI_feedback_Feedback extends taoQTI_models_classes_QTI_IdentifiedElement implements taoQTI_models_classes_QTI_container_FlowContainer
{

    protected $body = null;

    public function __construct($attributes = array(), taoQTI_models_classes_QTI_Item $relatedItem = null, $serial = ''){
        parent::__construct($attributes, $relatedItem, $serial);
        $this->body = new taoQTI_models_classes_QTI_container_ContainerStatic('', $relatedItem);//@todo: implement interactive container
    }

    public function getBody(){
        return $this->body;
    }

    protected function getUsedAttributes(){
        return array(
            'taoQTI_models_classes_QTI_attribute_OutcomeIdentifier',
            'taoQTI_models_classes_QTI_attribute_ShowHideTemplateElement'
        );
    }

    /**
     * Check if the given new identifier is valid in the current state of the qti element
     * 
     * @param string $newIdentifier
     * @return booean
     * @throws InvalidArgumentException
     */
    public function isIdentifierAvailable($newIdentifier){

        $returnValue = false;

        if(empty($newIdentifier) || is_null($newIdentifier)){
            throw new InvalidArgumentException("newIdentifier must be set");
        }

        if(!empty($this->identifier) && $newIdentifier == $this->identifier){
            $returnValue = true;
        }else{
            $relatedItem = $this->getRelatedItem();
            if(is_null($relatedItem)){
                $returnValue = true; //no restriction on identifier since not attached to any qti item
            }else{

                $collection = $relatedItem->getIdentifiedElements();

                try{
                    $feedback = $collection->getUnique($newIdentifier, 'taoQTI_models_classes_QTI_feedback_Feedback');
                    if(is_null($feedback)){
                        $returnValue = true;
                    }
                }catch(taoQTI_models_classes_QTI_QtiModelException $e){
                    //return false
                }
            }
        }

        return $returnValue;
    }

}
/* end of class taoQTI_models_classes_QTI_feedback_Feedback */