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
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10259
 * @subpackage actions_QTIform_interaction
 */
class taoQTI_actions_QTIform_interaction_MediaInteraction
    extends taoQTI_actions_QTIform_interaction_BlockInteraction
{

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     */
    public function initElements()
    {
		
		parent::setCommonElements();
		
        $interaction = $this->getInteraction();
		$object = $interaction->getObject();
		
		//add the object form:
		$objectSrcElt = tao_helpers_form_FormFactory::getElement('object_data', 'Textbox');
		$objectSrcElt->setAttribute('class', 'qti-file-img-interaction');
		$objectSrcElt->setDescription(__('Media source url'));
		
		$objectWidthElt = tao_helpers_form_FormFactory::getElement('object_width', 'Textbox');
		$objectWidthElt->setDescription(__('Media width'));
		
		$objectHeightElt = tao_helpers_form_FormFactory::getElement('object_height', 'Textbox');
		$objectHeightElt->setDescription(__('Media height'));
		
        $objectSrcElt->setValue($object->attr('data'));
        $objectWidthElt->setValue($object->attr('width'));
        $objectHeightElt->setValue($object->attr('height'));
		
		$this->form->addElement($objectSrcElt);
		$this->form->addElement($objectWidthElt);
		$this->form->addElement($objectHeightElt);
        
		//shuffle element:		
		$this->form->addElement(taoQTI_actions_QTIform_AssessmentItem::createBooleanElement($interaction, 'autostart', __('Autostart')));
        
        
//		$this->form->addElement(taoQTI_actions_QTIform_AssessmentItem::createTextboxElement($interaction, 'minPlays', __('Minimum Play')));//not implemented on runtime
		$this->form->addElement(taoQTI_actions_QTIform_AssessmentItem::createTextboxElement($interaction, 'maxPlays', __('Maximum Play')));
        
		$this->form->addElement(taoQTI_actions_QTIform_AssessmentItem::createBooleanElement($interaction, 'loop', __('Loop')));
		
    }

}