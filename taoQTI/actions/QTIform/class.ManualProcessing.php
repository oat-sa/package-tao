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
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoQTI/actions/QTIform/class.ManualProcessing.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 31.01.2012, 17:35:13 with ArgoUML PHP module
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage actions_QTIform
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoQTI_actions_QTIform_ResponseProcessingOptions
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoQTI/actions/QTIform/class.ResponseProcessingOptions.php');

/* user defined includes */
// section 127-0-1-1-249123f:13519689c9e:-8000:000000000000368A-includes begin
// section 127-0-1-1-249123f:13519689c9e:-8000:000000000000368A-includes end

/* user defined constants */
// section 127-0-1-1-249123f:13519689c9e:-8000:000000000000368A-constants begin
// section 127-0-1-1-249123f:13519689c9e:-8000:000000000000368A-constants end

/**
 * Short description of class taoQTI_actions_QTIform_ManualProcessing
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage actions_QTIform
 */
class taoQTI_actions_QTIform_ManualProcessing
    extends taoQTI_actions_QTIform_ResponseProcessingOptions
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute outcome
     *
     * @access public
     * @var Outcome
     */
    public $outcome = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Interaction interaction
     * @param  ResponseProcessing responseProcessing
     * @param  Outcome outcome
     * @return mixed
     */
    public function __construct( taoQTI_models_classes_QTI_interaction_Interaction $interaction,  taoQTI_models_classes_QTI_response_ResponseProcessing $responseProcessing,  taoQTI_models_classes_QTI_OutcomeDeclaration $outcome)
    {
        // section 127-0-1-1--3304025a:135345a8f39:-8000:00000000000036B0 begin
        $this->outcome = $outcome;
    	if (!$responseProcessing instanceof taoQTI_models_classes_QTI_response_Composite) {
    		throw new common_exception_Error('Call to manualprocessing form in non-composite mode');
    	}
        parent::__construct($interaction, $responseProcessing);
        // section 127-0-1-1--3304025a:135345a8f39:-8000:00000000000036B0 end
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
        // section 127-0-1-1-249123f:13519689c9e:-8000:0000000000003690 begin
    	parent::initElements();
    	$irp = $this->responseProcessing->getInteractionResponseProcessing($this->interaction->getResponse());
    	if (!$irp instanceof taoQTI_models_classes_QTI_response_interactionResponseProcessing_None) {
    		throw new common_exception_Error('Call to manualprocessing form on a non manual interaction');
    	}

		$serialElt = tao_helpers_form_FormFactory::getElement('outcomeSerial', 'Hidden');
		$serialElt->setValue($this->outcome->getSerial());
		$this->form->addElement($serialElt);

		//guidlines correct:
		$guidelines = tao_helpers_form_FormFactory::getElement('guidelines', 'Textarea');
		$guidelines->setDescription(__('Guidelines'));
		$guidelines->setValue($this->outcome->getAttributeValue('interpretation'));
		$this->form->addElement($guidelines);
		$correct = tao_helpers_form_FormFactory::getElement('correct', 'Textarea');
		$correct->setDescription(__('Correct answer'));
		$responses = $this->interaction->getResponse()->getCorrectResponses();
		$correct->setValue(implode("\n", $responses));
		$this->form->addElement($correct);
		/*
		$default = tao_helpers_form_FormFactory::getElement('defaultValue', 'Textbox');
		$default->setDescription(__('Empty response value'));
		$default->setValue($irp->getDefaultValue());
		$this->form->addElement($default);
		*/
		//scale
		$scale = $this->outcome->getScale();
		$availableOptions = array(
			tao_helpers_Uri::encode(taoItems_models_classes_Scale_Discrete::CLASS_URI) => __('Discrete Scale')
		);
		$scaleTypeElt = tao_helpers_form_FormFactory::getElement('scaletype', 'Combobox');
		$scaleTypeElt->setDescription(__('Scale type'));
		$scaleTypeElt->setEmptyOption(' ');
		$scaleTypeElt->setOptions($availableOptions);
		if (!is_null($scale)) {
			$scaleTypeElt->setValue($scale->getClassUri());
		}
		$this->form->addElement($scaleTypeElt);

		if (!is_null($scale)) {
			if ($scale->getClassUri() == taoItems_models_classes_Scale_Discrete::CLASS_URI) {
				$lowerBoundElt = tao_helpers_form_FormFactory::getElement('min', 'Textbox');
				$lowerBoundElt->setDescription(__('Minimum value'));
				$lowerBoundElt->setValue($scale->lowerBound);
				$lowerBoundElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
				$lowerBoundElt->addValidator(tao_helpers_form_FormFactory::getValidator('Integer', array('min' => 0)));
				$this->form->addElement($lowerBoundElt);

				$upperBoundElt = tao_helpers_form_FormFactory::getElement('max', 'Textbox');
				$upperBoundElt->setDescription(__('Maximum value'));
				$upperBoundElt->setValue($scale->upperBound);
				$upperBoundElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
				$upperBoundElt->addValidator(tao_helpers_form_FormFactory::getValidator('Integer', array('min' => 0, 'integer2_ref' => $lowerBoundElt, 'comparator' => '>')));
				$this->form->addElement($upperBoundElt);

				$distanceElt = tao_helpers_form_FormFactory::getElement('dist', 'Textbox');
				$distanceElt->setDescription(__('Distance'));
				$distanceElt->setValue($scale->distance);
				$distanceElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
				$distanceElt->addValidator(tao_helpers_form_FormFactory::getValidator('Integer', array('min' => 0)));
				$this->form->addElement($distanceElt);
			} else {
				//@todo scale not supported message
			}
		}
		// section 127-0-1-1-249123f:13519689c9e:-8000:0000000000003690 end
    }

} /* end of class taoQTI_actions_QTIform_ManualProcessing */

?>