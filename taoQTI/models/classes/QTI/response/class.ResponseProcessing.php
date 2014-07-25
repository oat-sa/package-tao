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
 * Short description of class
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response
 */
abstract class taoQTI_models_classes_QTI_response_ResponseProcessing extends taoQTI_models_classes_QTI_Element
{

    /**
     * Short description of method create
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Item item
     * @return taoQTI_models_classes_QTI_response_ResponseProcessing
     */
    public static function create(taoQTI_models_classes_QTI_Item $item){

        throw new common_Exception('create not implemented for '.get_called_class());

        return $returnValue;
    }

    /**
     * Short description of method takeoverFrom
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  ResponseProcessing responseProcessing
     * @param  Item item
     * @return taoQTI_actions_QTIform_ResponseProcessing
     */
    public static function takeoverFrom(taoQTI_models_classes_QTI_response_ResponseProcessing $responseProcessing, taoQTI_models_classes_QTI_Item $item){

        throw new taoQTI_models_classes_QTI_response_TakeoverFailedException('takeoverFrom not implemented for '.get_called_class());

        return $returnValue;
    }

    /**
     * Short description of method getForm
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Response response
     * @return tao_helpers_form_Form
     */
    public function getForm(taoQTI_models_classes_QTI_ResponseDeclaration $response){
        return null;
    }

    /**
     * Short description of method takeNoticeOfAddedInteraction
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Interaction interaction
     * @param  Item item
     * @return mixed
     */
    public function takeNoticeOfAddedInteraction(taoQTI_models_classes_QTI_interaction_Interaction $interaction, taoQTI_models_classes_QTI_Item $item){
        
    }

    /**
     * Short description of method takeNoticeOfRemovedInteraction
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Interaction interaction
     * @param  Item item
     * @return mixed
     */
    public function takeNoticeOfRemovedInteraction(taoQTI_models_classes_QTI_interaction_Interaction $interaction, taoQTI_models_classes_QTI_Item $item){
        
    }

    /**
     * 
     * @return array
     */
    protected function getUsedAttributes(){
        //currently not used
        return array();
    }

    public function toArray(){
        return array();
    }

}
/* end of abstract class taoQTI_models_classes_QTI_response_ResponseProcessing */