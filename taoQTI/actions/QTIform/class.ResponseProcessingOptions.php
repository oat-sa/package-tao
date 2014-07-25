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
 * Short description of class taoQTI_actions_QTIform_ResponseProcessingOptions
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage actions_QTIform
 */
abstract class taoQTI_actions_QTIform_ResponseProcessingOptions
    extends tao_helpers_form_FormContainer
{

    /**
     * Short description of attribute interaction
     *
     * @access protected
     * @var Interaction
     */
    protected $interaction = null;

    /**
     * Short description of attribute responseProcessing
     *
     * @access protected
     * @var ResponseProcessing
     */
    protected $responseProcessing = null;

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Interaction interaction
     * @param  ResponseProcessing responseProcessing
     * @return mixed
     */
    public function __construct( taoQTI_models_classes_QTI_interaction_Interaction $interaction,  taoQTI_models_classes_QTI_response_ResponseProcessing $responseProcessing)
    {
        if(is_null($interaction) || is_null($responseProcessing)){
			throw new common_exception_Error('interaction and responseProcessing cannot be null');
		}
		$this->interaction = $interaction;
		$this->responseProcessing = $responseProcessing;
		parent::__construct(array(), array('option1' => ''));
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
		$this->form = tao_helpers_form_FormFactory::getForm('ResponseCodingOptionsForm');
		
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
        $serialElt = tao_helpers_form_FormFactory::getElement('interactionSerial', 'Hidden');
		$serialElt->setValue($this->interaction->getSerial());
		$this->form->addElement($serialElt);
		
        $serialElt = tao_helpers_form_FormFactory::getElement('responseprocessingSerial', 'Hidden');
		$serialElt->setValue($this->responseProcessing->getSerial());
		$this->form->addElement($serialElt);
    }

}