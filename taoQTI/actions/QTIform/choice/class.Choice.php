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
 * Short description of class taoQTI_actions_QTIform_choice_Choice
 *
 * @abstract
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10254
 * @subpackage actions_QTIform_choice
 */
abstract class taoQTI_actions_QTIform_choice_Choice extends tao_helpers_form_FormContainer
{

    /**
     * Short description of attribute choice
     *
     * @access protected
     * @var Data
     */
    protected $choice = null;

    /**
     * Short description of attribute formName
     *
     * @access protected
     * @var string
     */
    protected $formName = 'ChoiceForm_';

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  Data choice
     */
    public function __construct(taoQTI_models_classes_QTI_Element $choice = null){
        $this->choice = $choice;
        $this->formName = 'ChoiceForm_'.$this->choice->getSerial();
        parent::__construct(array(), array());
    }

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     */
    public function initForm(){
        $this->form = tao_helpers_form_FormFactory::getForm($this->formName);
        $this->form->setActions(array(), 'bottom');
        //no save elt required, all shall be done with ajax request
    }

    /**
     * Short description of method setCommonElements
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     */
    public function setCommonElements(){
        //add hidden id element, to know what the old id is:
        $oldIdElt = tao_helpers_form_FormFactory::getElement('choiceSerial', 'Hidden');
        $oldIdElt->setValue($this->choice->getSerial());
        $this->form->addElement($oldIdElt);

        //id element: need for checking unicity
        $labelElt = tao_helpers_form_FormFactory::getElement('choiceIdentifier', 'Textbox');
        $labelElt->setDescription(__('Identifier'));
        $labelElt->setValue($this->choice->getIdentifier());
        $this->form->addElement($labelElt);

        //the fixed attribute element
        $fixedElt = tao_helpers_form_FormFactory::getElement('fixed', 'Checkbox');
        $fixedElt->setDescription(__('Fixed'));
        $fixedElt->setOptions(array('true' => '')); //empty label because the description of the element is enough
        $fixed = $this->choice->getAttributeValue('fixed');
        if(!empty($fixed)){
            if($fixed === 'true' || $fixed === true){
                $fixedElt->setValue('true');
            }
        }
        $this->form->addElement($fixedElt);
    }

    /**
     * Short description of method getChoice
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return taoQTI_models_classes_QTI_Element
     */
    public function getChoice(){
        return $this->choice;
    }

}