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
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10304
 * @subpackage actions_QTIform_interaction
 */
class taoQTI_actions_QTIform_interaction_ExtendedtextInteraction
    extends taoQTI_actions_QTIform_interaction_StringInteraction
{
    /**
     * Short description of method initElements
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return mixed
     */
    public function initElements()
    {
		$interaction = $this->getInteraction();
		
		//the prompt field is the interaction's data for a block interaction: an extend text interaction is both a "string" and a "block" interaction
		$promptElt = tao_helpers_form_FormFactory::getElement('prompt', 'Textarea');//should be a text... need to solve the conflict with the 
		$promptElt->setAttribute('class', 'qti-html-area');
		$promptElt->setDescription(__('Prompt'));
		$promptBody = taoQTI_models_classes_QtiAuthoringService::singleton()->getFilteredData($this->interaction->getPromptObject());
		if(!empty($promptBody)){
			$promptElt->setValue($promptBody);
		}
		$this->form->addElement($promptElt);
		
		//set common elements of string interaction:
		parent::setCommonElements();
				
		//other elements:		
		$maxStringsElt = tao_helpers_form_FormFactory::getElement('maxStrings', 'Textbox');
		$maxStringsElt->setDescription(__('Maximum number of separate strings'));
		//validator: is int??
		$maxStrings = $interaction->getAttributeValue('maxStrings');
		if(!empty($maxStrings)){
			$maxStringsElt->setValue($maxStrings);
		}
		$this->form->addElement($maxStringsElt);
		
		$expectedLinesElt = tao_helpers_form_FormFactory::getElement('expectedLines', 'Textbox');
		$expectedLinesElt->setDescription(__('Expected lines'));
		$expectedLines = $interaction->getAttributeValue('expectedLines');
		if(!empty($expectedLines)){
			$expectedLinesElt->setValue($expectedLines);
		}
		$this->form->addElement($expectedLinesElt);
		
    }

}