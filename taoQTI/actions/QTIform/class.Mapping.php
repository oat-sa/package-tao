<?php
/**  
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
 * Short description of class taoQTI_actions_QTIform_Mapping
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10066
 * @subpackage actions_QTIform
 */
class taoQTI_actions_QTIform_Mapping
    extends taoQTI_actions_QTIform_ResponseProcessingOptions
{


    /**
     * Short description of method initElements
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function initElements()
    {
        
        parent::initElements();
		$response = $this->interaction->getResponse();
		//default box:
		$defaultValueElt = tao_helpers_form_FormFactory::getElement('defaultValue', 'Textbox');
		$defaultValueElt->setDescription(__('Score mapping default value'));
		$defaultValue = 0;
		$mappingDefaultValue = $response->getMappingDefaultValue();
		if(empty($mappingDefaultValue)){
			$response->setMappingDefaultValue($defaultValue);
		}else{
			$defaultValue = $mappingDefaultValue;
		}
		$defaultValueElt->setValue($defaultValue);
		$this->form->addElement($defaultValueElt);
		
		$lowerBoundElt = tao_helpers_form_FormFactory::getElement('lowerBound', 'Textbox');
		$lowerBoundElt->setDescription(__('Score lower bound'));
		
        $upperBoundElt = tao_helpers_form_FormFactory::getElement('upperBound', 'Textbox');
		$upperBoundElt->setDescription(__('Score upper bound'));
        
		$mappingOptions = $response->getAttributeValue('mapping');
		if(is_array($mappingOptions)){
			if(isset($mappingOptions['upperBound'])) {
                $upperBoundElt->setValue($mappingOptions['upperBound']);
            }
			if(isset($mappingOptions['lowerBound'])) {
                $lowerBoundElt->setValue($mappingOptions['lowerBound']);
            }
		}
		
		$this->form->addElement($lowerBoundElt);
        $this->form->addElement($upperBoundElt);
    }

}