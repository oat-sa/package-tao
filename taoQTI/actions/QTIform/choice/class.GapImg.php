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
 * Short description of class taoQTI_actions_QTIform_choice_GapImg
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10290
 * @subpackage actions_QTIform_choice
 */
class taoQTI_actions_QTIform_choice_GapImg extends taoQTI_actions_QTIform_choice_AssociableChoice
{

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     */
    public function initElements(){

        parent::setCommonElements();

        $object = $this->choice->getObject();

        //the image label: 
        $objectLabelElt = tao_helpers_form_FormFactory::getElement('objectLabel', 'Textbox');
        $objectLabelElt->setDescription(__('Image label'));
        $objectLabel = (string) $this->choice->getAttributeValue('objectLabel');
        $objectLabelElt->setValue($objectLabel);
        $this->form->addElement($objectLabelElt);

        //add the object form:
        $objectSrcElt = tao_helpers_form_FormFactory::getElement('object_data', 'Textbox');
        $objectSrcElt->setAttribute('class', 'qti-file-img qti-with-preview qti-with-resizer');
        $objectSrcElt->setDescription(__('Image source url'));

        $objectWidthElt = tao_helpers_form_FormFactory::getElement('object_width', 'Textbox');
        $objectWidthElt->setDescription(__('Image width'));

        $objectHeightElt = tao_helpers_form_FormFactory::getElement('object_height', 'Textbox');
        $objectHeightElt->setDescription(__('Image height'));

        //note: no type element since it must be determined by the image type

        $objectSrcElt->setValue($object->attr('data'));
        $objectWidthElt->setValue($object->attr('width'));
        $objectHeightElt->setValue($object->attr('height'));

        $this->form->addElement($objectSrcElt);
        $this->form->addElement($objectWidthElt);
        $this->form->addElement($objectHeightElt);

        $matchMaxElt = tao_helpers_form_FormFactory::getElement('matchMax', 'Textbox');
        $matchMaxElt->setDescription(__('Maximal number of matching'));
        $matchMax = (string) $this->choice->getAttributeValue('matchMax');
        $matchMaxElt->setValue($matchMax);
        $this->form->addElement($matchMaxElt);

        $this->form->createGroup('choicePropOptions_'.$this->choice->getSerial(), __('Advanced properties'), array('fixed', 'object_width', 'object_height', 'matchMax'));
    }

}