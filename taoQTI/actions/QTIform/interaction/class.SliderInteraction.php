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
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10341
 * @subpackage actions_QTIform_interaction
 */
class taoQTI_actions_QTIform_interaction_SliderInteraction
    extends taoQTI_actions_QTIform_interaction_BlockInteraction
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
		parent::setCommonElements();
		$interaction = $this->getInteraction();
		$this->form->addElement(taoQTI_actions_QTIform_AssessmentItem::createTextboxElement($interaction, 'lowerBound', __('Lower bound')));//mendatory 0
        $this->form->addElement(taoQTI_actions_QTIform_AssessmentItem::createTextboxElement($interaction, 'upperBound', __('Upper bound')));//mendatory 10
        $this->form->addElement(taoQTI_actions_QTIform_AssessmentItem::createTextboxElement($interaction, 'step', __('Step')));
		
		//the very optional step label attr is temporaily disabled in authoring since the runtime does not support it properly
        //$this->form->addElement(taoQTI_actions_QTIform_AssessmentItem::createBooleanElement($interaction, 'stepLabel', __('Step label display')));//false
		
		$orientationElt = tao_helpers_form_FormFactory::getElement('orientation', 'Combobox');
		$orientationElt->setDescription(__('Orientation'));
		$orientationElt->setOptions(array(
            'horizontal' => __('horizontal'),
			'vertical' => __('vertical')
		));
		$orientation = $interaction->getAttributeValue('orientation');
		if(!empty($orientation)){
			if($orientation === 'vertical' || $orientation === 'horizontal'){
				$orientationElt->setValue($orientation);
			}
		}
		$this->form->addElement($orientationElt);

		$this->form->addElement(taoQTI_actions_QTIform_AssessmentItem::createBooleanElement($interaction, 'reverse', __('Reverse rendering')));//false		
    }

}