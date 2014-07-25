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
 */

/**
 * The response processing of a single interaction
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response_interactionResponseProcessing
 */
abstract class taoQTI_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing implements taoQTI_models_classes_QTI_response_Rule
{
    /**
     * Short description of attribute SCORE_PREFIX
     *
     * @access private
     * @var string
     */

    const SCORE_PREFIX = 'SCORE_';

    /**
     * Short description of attribute response
     *
     * @access public
     * @var Response
     */
    public $response = null;

    /**
     * Short description of attribute outcome
     *
     * @access public
     * @var Outcome
     */
    public $outcome = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRule(){
        $returnValue = (string) '';

        throw new common_Exception('Missing getRule implementation for '.get_class($this), array('TAOITEMS', 'QTI', 'HARD'));

        return (string) $returnValue;
    }

    /**
     * Short description of method create
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int classID
     * @param  Response response
     * @param  Item item
     * @return taoQTI_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing
     */
    public static function create($classID, taoQTI_models_classes_QTI_ResponseDeclaration $response, taoQTI_models_classes_QTI_Item $item){
        switch($classID){
            case taoQTI_models_classes_QTI_response_interactionResponseProcessing_None::CLASS_ID :
                $className = "taoQTI_models_classes_QTI_response_interactionResponseProcessing_None";
                break;
            case taoQTI_models_classes_QTI_response_interactionResponseProcessing_MatchCorrectTemplate::CLASS_ID :
                $className = "taoQTI_models_classes_QTI_response_interactionResponseProcessing_MatchCorrectTemplate";
                break;
            case taoQTI_models_classes_QTI_response_interactionResponseProcessing_MapResponseTemplate::CLASS_ID :
                $className = "taoQTI_models_classes_QTI_response_interactionResponseProcessing_MapResponseTemplate";
                break;
            case taoQTI_models_classes_QTI_response_interactionResponseProcessing_MapResponsePointTemplate::CLASS_ID :
                $className = "taoQTI_models_classes_QTI_response_interactionResponseProcessing_MapResponsePointTemplate";
                break;
            case taoQTI_models_classes_QTI_response_interactionResponseProcessing_Custom::CLASS_ID :
                $className = "taoQTI_models_classes_QTI_response_interactionResponseProcessing_Custom";
                break;
            default :
                throw new common_exception_Error('Unknown InteractionResponseProcessing Class ID "'.$classID.'"');
        }
        $outcome = self::generateOutcomeDefinition();
        $outcomes = $item->getOutcomes();
        $outcomes[] = $outcome;
        $item->setOutcomes($outcomes);
        $returnValue = new $className($response, $outcome);

        return $returnValue;
    }

    /**
     * Short description of method generateOutcomeDefinition
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return taoQTI_models_classes_QTI_OutcomeDeclaration
     */
    public static function generateOutcomeDefinition(){
        return new taoQTI_models_classes_QTI_OutcomeDeclaration(array('baseType' => 'integer', 'cardinality' => 'single'));
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Response response
     * @param  Outcome outcome
     * @return mixed
     */
    public function __construct(taoQTI_models_classes_QTI_ResponseDeclaration $response, taoQTI_models_classes_QTI_OutcomeDeclaration $outcome){
        $this->response = $response;
        $this->outcome = $outcome;
    }

    /**
     * Short description of method getResponse
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return taoQTI_models_classes_QTI_ResponseDeclaration
     */
    public function getResponse(){
        return $this->response;
    }

    /**
     * Short description of method getOutcome
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return taoQTI_models_classes_QTI_OutcomeDeclaration
     */
    public function getOutcome(){
        return $this->outcome;
    }

    /**
     * Short description of method getIdentifier
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getIdentifier(){
        $returnValue = $this->getResponse()->getIdentifier().'_rp';
        return (string) $returnValue;
    }

}