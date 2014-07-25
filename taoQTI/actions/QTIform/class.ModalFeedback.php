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
 * Short description of class taoQTI_actions_QTIform_EditObject
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @subpackage actions_QTIform
 */
class taoQTI_actions_QTIform_ModalFeedback extends tao_helpers_form_FormContainer
{

    /**
     * Short description of attribute object
     *
     * @access public
     * @var Object
     */
    public $feedback = null;

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  Object object
     * @param  Item item
     * @return mixed
     */
    public function __construct(taoQTI_models_classes_QTI_feedback_ModalFeedback $feedback){
        $this->feedback = $feedback;
        parent::__construct();
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return mixed
     */
    public function initForm(){
        $this->form = tao_helpers_form_FormFactory::getForm('EditModalFeedback');
        $this->form->setActions(array(), 'bottom');
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return mixed
     */
    public function initElements(){
        
        $titleElt = taoQTI_actions_QTIform_AssessmentItem::createTextboxElement($this->feedback, 'title');
        $this->form->addElement($titleElt);
        
        $dataElt = tao_helpers_form_FormFactory::getElement('data', 'Textarea');
		$dataElt->setAttribute('class', 'qti-html-area');
		$dataElt->setDescription(__('Body'));
        $bodyData = taoQTI_models_classes_QtiAuthoringService::singleton()->getFilteredData($this->feedback);
		if(!empty($bodyData)){
			$dataElt->setValue($bodyData);
		}
		$this->form->addElement($dataElt);
        
    }

}