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
 * Short description of class taoQTI_actions_QTIform_choice_GapText
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10289
 * @subpackage actions_QTIform_choice
 */
class taoQTI_actions_QTIform_choice_GapText
    extends taoQTI_actions_QTIform_choice_AssociableChoice
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
		
		//add Textbox:
		$dataElt = tao_helpers_form_FormFactory::getElement('data', 'Textbox');
		$dataElt->setDescription(__('Value'));
		$choiceData = (string) $this->choice->getContent();
		if(!empty($choiceData)){
			$dataElt->setValue($choiceData);
		}
		$this->form->addElement($dataElt);
		
		$matchMaxElt = tao_helpers_form_FormFactory::getElement('matchMax', 'Textbox');
		$matchMaxElt->setDescription(__('Maximal number of matching'));
		$matchMax = (string) $this->choice->getAttributeValue('matchMax');
		$matchMaxElt->setValue($matchMax);
		$this->form->addElement($matchMaxElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->choice->getSerial(), __('Advanced properties'), array('fixed', 'matchMax'));
		
    }

}