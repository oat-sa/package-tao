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
 * Short description of class taoQTI_actions_QTIform_interaction_Interaction
 *
 * @abstract
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10247
 * @subpackage actions_QTIform_interaction
 */
abstract class taoQTI_actions_QTIform_interaction_Interaction
    extends tao_helpers_form_FormContainer
{
    protected $interaction = null;

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  Interaction interaction
     */
    public function __construct( taoQTI_models_classes_QTI_interaction_Interaction $interaction)
    {
		$this->interaction = $interaction;
		parent::__construct(array(), array());
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     */
    public function initForm()
    {
		$this->form = tao_helpers_form_FormFactory::getForm('InteractionForm');
		
		//custom actions only:
		$actions = array();
		
		$saveElt = tao_helpers_form_FormFactory::getElement('save', 'Free');
		$saveElt->setValue("<a href='#' class='interaction-form-submitter' ><img src='".BASE_WWW."img/qtiAuthoring/update.png'  /> ".__('Save Interaction & Choices')."</a>");
		$actions[] = $saveElt;
		
		$this->form->setActions($actions, 'top');
		$this->form->setActions(array(), 'bottom');
        
    }

    /**
     * Short description of method getInteraction
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return taoQTI_models_classes_QTI_interaction_Interaction
     */
    public function getInteraction()
    {
		return $this->interaction;
    }

    /**
     * Short description of method setCommonElements
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     */
    public function setCommonElements()
    {
		//add hidden serial element:
		$oldIdElt = tao_helpers_form_FormFactory::getElement('interactionSerial', 'Hidden');
		$oldIdElt->setValue($this->interaction->getSerial());
		$this->form->addElement($oldIdElt);
    }

}