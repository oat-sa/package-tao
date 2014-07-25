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
 * Helper to build the Interaction Response Processing Forms
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage helpers_qti
 */
class taoQTI_helpers_qti_InteractionAuthoring
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getIRPData
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @param  Interaction interaction
     * @return array
     */
    public static function getIRPData( taoQTI_models_classes_QTI_Item $item,  taoQTI_models_classes_QTI_interaction_Interaction $interaction)
    {
        $returnValue = array();

        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:0000000000003928 begin
		$responseProcessing = $item->getResponseProcessing();
		$response = $interaction->getResponse();
		
		if ($responseProcessing instanceof taoQTI_models_classes_QTI_response_TemplatesDriven) {
			// templates driven:
			common_Logger::d('template: '.$responseProcessing->getTemplate($response));
			if (taoQTI_helpers_qti_InteractionAuthoring::isResponseMappingMode($responseProcessing->getTemplate($response))) {
				$returnValue = self::getMapingRPData($item, $interaction);
			} else {
				$returnValue = self::getCorrectRPData($item, $interaction);
			}
			
		} elseif ($responseProcessing instanceof taoQTI_models_classes_QTI_response_Composite){
			
			// composite processing
			$irp = $responseProcessing->getInteractionResponseProcessing($interaction->getResponse());
			switch (get_class($irp)) {
				case 'taoQTI_models_classes_QTI_response_interactionResponseProcessing_None' :
					$returnValue = self::getManualRPData($item, $interaction);
					break;
				case 'taoQTI_models_classes_QTI_response_interactionResponseProcessing_MatchCorrectTemplate' :
					$returnValue = self::getCorrectRPData($item, $interaction);
					break;
				case 'taoQTI_models_classes_QTI_response_interactionResponseProcessing_MapResponseTemplate' :
				case 'taoQTI_models_classes_QTI_response_interactionResponseProcessing_MapResponsePointTemplate' :
					$returnValue = self::getMapingRPData($item, $interaction);
					break;
			}
			
		} else {
			$xhtmlForms[] = '<b>'
				.__('The response form is not available for the selected response processing.<br/>')
				.'</b>';
		}
        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:0000000000003928 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getManualRPData
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @param  Interaction interaction
     * @return core_kernel_classes_Array
     */
    private static function getManualRPData( taoQTI_models_classes_QTI_Item $item,  taoQTI_models_classes_QTI_interaction_Interaction $interaction)
    {
        $returnValue = null;

        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:000000000000392C begin
        $irp = $item->getResponseProcessing()->getInteractionResponseProcessing($interaction->getResponse());
        $outcome = null;
		foreach ($item->getOutcomes() as $outcomeCandidate) {
			if ($outcomeCandidate == $irp->getOutcome()) {
				$outcome = $outcomeCandidate;
				break; 
			}
		}
		if (is_null($outcome)) {
			throw new common_exception_Error(__('No outcome defined for interaction ').$interaction->getIdentifier());
		}
		$manualForm = new taoQTI_actions_QTIform_ManualProcessing($interaction, $item->getResponseProcessing(), $outcome);
		if (!is_null($manualForm)) {
			$xhtmlForms[] = $manualForm->getForm()->render();
		}
		$returnValue = array(
			'displayGrid'	=> false,
			'forms'			=> $xhtmlForms
		);
        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:000000000000392C end

        return $returnValue;
    }

    /**
     * Short description of method getMapingRPData
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @param  Interaction interaction
     * @return array
     */
    private static function getMapingRPData( taoQTI_models_classes_QTI_Item $item,  taoQTI_models_classes_QTI_interaction_Interaction $interaction)
    {
        $returnValue = array();

        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:000000000000392E begin
        $responseProcessing = $item->getResponseProcessing();
        $service = taoQTI_models_classes_QtiAuthoringService::singleton();
		$columnModel = $service->getInteractionResponseColumnModel($interaction, $item->getResponseProcessing(), true);
		$responseData = $service->getInteractionResponseData($interaction);
		
		$mappingForm = new taoQTI_actions_QTIform_Mapping($interaction, $item->getResponseProcessing());
		if (!is_null($mappingForm)) {
			$forms = array($mappingForm->getForm()->render());
		} else {
			common_Logger::w('Could not load qti mapping form', array('QTI', 'TAOITEMS'));
			$forms = array();
		}
		$returnValue = array(
			'displayGrid'	=> true,
			'data'			=> $responseData,
			'colModel'		=> $columnModel,
			'setResponseMappingMode' => true,
			'forms'			=> $forms
		);
        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:000000000000392E end

        return (array) $returnValue;
    }

    /**
     * Short description of method getCorrectRPData
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @param  Interaction interaction
     * @return array
     */
    private static function getCorrectRPData( taoQTI_models_classes_QTI_Item $item,  taoQTI_models_classes_QTI_interaction_Interaction $interaction)
    {
        $returnValue = array();

        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:0000000000003930 begin
        $service = taoQTI_models_classes_QtiAuthoringService::singleton();
		$columnModel = $service->getInteractionResponseColumnModel($interaction, $item->getResponseProcessing(), false);
		$responseData = $service->getInteractionResponseData($interaction);
		$returnValue = array(
			'displayGrid'	=> true,
			'data'			=> $responseData,
			'colModel'		=> $columnModel,
			'setResponseMappingMode' => false,
			'forms'			=> array()
		);
        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:0000000000003930 end

        return (array) $returnValue;
    }
    
    /**
     * Whenever or not the specified response processing template is 
     * in mapping mode or not
     * 
     * @param string $processingType
     */
	public static function isResponseMappingMode($processingType){
		return in_array($processingType, array(
			taoQTI_models_classes_QTI_response_Template::MAP_RESPONSE,
			taoQTI_models_classes_QTI_response_Template::MAP_RESPONSE_POINT
		));
	}

} /* end of class taoQTI_helpers_qti_InteractionAuthoring */

?>