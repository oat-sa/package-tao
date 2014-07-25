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
 * Short description of class taoQTI_actions_QTIform_CompositeResponseOptions
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage actions_QTIform
 */
class taoQTI_actions_QTIform_CompositeResponseOptions
    extends tao_helpers_form_FormContainer
{

    /**
     * Short description of attribute responseProcessing
     *
     * @access public
     * @var ResponseProcessing
     */
    public $responseProcessing = null;

    /**
     * Short description of attribute response
     *
     * @access public
     * @var ResponseDeclaration
     */
    public $response = null;

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  ResponseProcessing responseProcessing
     * @param  Response response
     * @return mixed
     */
    public function __construct( taoQTI_models_classes_QTI_response_ResponseProcessing $responseProcessing,  taoQTI_models_classes_QTI_ResponseDeclaration $response)
    {
		$this->responseProcessing = $responseProcessing;
        $this->response = $response;
        parent::__construct();
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        $this->form = tao_helpers_form_FormFactory::getForm('InteractionResponseProcessingForm');
		$this->form->setActions(array(), 'bottom');
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        $rpElt = tao_helpers_form_FormFactory::getElement('responseprocessingSerial', 'Hidden');
		$rpElt->setValue($this->responseProcessing->getSerial());
		$this->form->addElement($rpElt);
		
    	$serialElt = tao_helpers_form_FormFactory::getElement('responseSerial', 'Hidden');
		$serialElt->setValue($this->response->getSerial());
		$this->form->addElement($serialElt);
    	
		$currentClass = get_class($this->responseProcessing->getInteractionResponseProcessing($this->response));
		$currentIRP = $currentClass::CLASS_ID;
		
		$irps = array(
			taoQTI_models_classes_QTI_response_interactionResponseProcessing_MatchCorrectTemplate::CLASS_ID => __('correct'),
		);
		
		$interaction = $this->response->getAssociatedInteraction();
		if(!is_null($interaction)){
			switch(strtolower($interaction->getType())){
				case 'order':
				case 'graphicorder':{
					break;
				}
				case 'selectpoint';
				case 'positionobject':{
					$irps[taoQTI_models_classes_QTI_response_interactionResponseProcessing_MapResponsePointTemplate::CLASS_ID] = __('map point');
					break;
				}
				default:{
					$irps[taoQTI_models_classes_QTI_response_interactionResponseProcessing_MapResponseTemplate::CLASS_ID] = __('map');
				}
			}
		}
		
		if ($currentIRP == taoQTI_models_classes_QTI_response_interactionResponseProcessing_Custom::CLASS_ID) {
			$irps[taoQTI_models_classes_QTI_response_interactionResponseProcessing_Custom::CLASS_ID] = __('custom');			
		}
		
		$InteractionResponseProcessing = tao_helpers_form_FormFactory::getElement('interactionResponseProcessing', 'Combobox');
		$InteractionResponseProcessing->setDescription(__('Processing type'));
		$InteractionResponseProcessing->setOptions($irps);
		$InteractionResponseProcessing->setValue($currentIRP);
		$this->form->addElement($InteractionResponseProcessing);
    }

}