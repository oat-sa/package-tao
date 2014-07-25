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
 * Short description of class taoQTI_models_classes_QTI_response_Composite
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response
 */
abstract class taoQTI_models_classes_QTI_response_Composite extends taoQTI_models_classes_QTI_response_ResponseProcessing implements taoQTI_models_classes_QTI_response_Rule
{

    /**
     * Short description of attribute components
     *
     * @access protected
     * @var array
     */
    protected $components = array();

    /**
     * Short description of attribute outcomeIdentifier
     *
     * @access protected
     * @var string
     */
    protected $outcomeIdentifier = '';

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRule(){
        $returnValue = (string) '';

        foreach($this->components as $irp){
            $returnValue .= $irp->getRule();
        }
        foreach($this->getCompositionRules() as $rule){
            $returnValue .= $rule->getRule();
        }

        return (string) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @param  string outcomeIdentifier
     * @return mixed
     */
    public function __construct(taoQTI_models_classes_QTI_Item $item, $outcomeIdentifier = 'SCORE'){
        parent::__construct();
        $this->outcomeIdentifier = $outcomeIdentifier;
        $outcomeExists = false;
        foreach($item->getOutcomes() as $outcome){
            if($outcome->getIdentifier() == $outcomeIdentifier){
                $outcomeExists = true;
                break;
            }
        }
        if(!$outcomeExists){
            $outcomes = $item->getOutcomes();
            $outcomes[] = new taoQTI_models_classes_QTI_OutcomeDeclaration(array('identifier' => $outcomeIdentifier, 'baseType' => 'float', 'cardinality' => 'single'));
            $item->setOutcomes($outcomes);
        }
    }

    /**
     * Short description of method create
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @return taoQTI_models_classes_QTI_response_ResponseProcessing
     */
    public static function create(taoQTI_models_classes_QTI_Item $item){
        $returnValue = new taoQTI_models_classes_QTI_response_Summation($item);
        foreach($item->getInteractions() as $interaction){
            $irp = taoQTI_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing::create(
                            taoQTI_models_classes_QTI_response_interactionResponseProcessing_None::CLASS_ID
                            , $interaction->getResponse()
                            , $item
            );
            $returnValue->add($irp, $item);
        }

        return $returnValue;
    }

    /**
     * Short description of method takeOverFrom
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  ResponseProcessing responseProcessing
     * @param  Item item
     * @return taoQTI_models_classes_QTI_response_Composite
     */
    public static function takeOverFrom(taoQTI_models_classes_QTI_response_ResponseProcessing $responseProcessing, taoQTI_models_classes_QTI_Item $item){
        $returnValue = null;

        if($responseProcessing instanceof static){
            // already good
            $returnValue = $responseProcessing;
        }elseif($responseProcessing instanceof taoQTI_models_classes_QTI_response_Template){
            // IMS Template
            $rp = new taoQTI_models_classes_QTI_response_Summation($item, 'SCORE');
            foreach($item->getInteractions() as $interaction){
                $response = $interaction->getResponse();
                try{
                    $irp = taoQTI_models_classes_QTI_response_interactionResponseProcessing_Template::createByTemplate(
                                    $responseProcessing->getUri(), $response, $item);
                }catch(Exception $e){
                    throw new taoQTI_models_classes_QTI_response_TakeoverFailedException();
                }
                $rp->add($irp, $item);
            }
            $returnValue = $rp;
        }elseif($responseProcessing instanceof taoQTI_models_classes_QTI_response_TemplatesDriven){
            // TemplateDriven
            $rp = new taoQTI_models_classes_QTI_response_Summation($item, 'SCORE');
            foreach($item->getInteractions() as $interaction){
                $response = $interaction->getResponse();
                try{
                    $irp = taoQTI_models_classes_QTI_response_interactionResponseProcessing_Template::createByTemplate(
                                    $responseProcessing->getTemplate($response)
                                    , $response
                                    , $item
                    );
                }catch(Exception $e){
                    throw new taoQTI_models_classes_QTI_response_TakeoverFailedException();
                }
                $rp->add($irp, $item);
            }
            $returnValue = $rp;
        }else{
            common_Logger::d('Composite ResponseProcessing can not takeover from '.get_class($responseProcessing).' yet');
            throw new taoQTI_models_classes_QTI_response_TakeoverFailedException();
        }

        common_Logger::i('Converted to Composite', array('TAOITEMS', 'QTI'));

        return $returnValue;
    }

    /**
     * Short description of method add
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  InteractionResponseProcessing interactionResponseProcessing
     * @return mixed
     */
    public function add(taoQTI_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing $interactionResponseProcessing){
        $this->components[] = $interactionResponseProcessing;
    }

    /**
     * Short description of method getInteractionResponseProcessing
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Response response
     * @return taoQTI_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing
     */
    public function getInteractionResponseProcessing(taoQTI_models_classes_QTI_ResponseDeclaration $response){
        foreach($this->components as $irp){
            if($irp->getResponse() == $response){
                $returnValue = $irp;
                break;
            }
        }
        if(is_null($returnValue)){
            throw new common_Exception('No interactionResponseProcessing defined for '.$response->getIdentifier());
        }
        return $returnValue;
    }

    /**
     * Short description of method getIRPByOutcome
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Outcome outcome
     * @return taoQTI_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing
     */
    public function getIRPByOutcome(taoQTI_models_classes_QTI_OutcomeDeclaration $outcome){
        foreach($this->components as $irp){
            if($irp->getOutcome() == $outcome){
                $returnValue = $irp;
                break;
            }
        }

        return $returnValue;
    }

    /**
     * Short description of method replace
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  InteractionResponseProcessing newInteractionResponseProcessing
     * @return mixed
     */
    public function replace(taoQTI_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing $newInteractionResponseProcessing){
        $oldkey = null;
        foreach($this->components as $key => $component){
            if($component->getResponse() == $newInteractionResponseProcessing->getResponse()){
                $oldkey = $key;
                break;
            }
        }
        if(!is_null($oldkey)){
            unset($this->components[$oldkey]);
        }else{
            common_Logger::w('Component to be replaced not found', array('TAOITEMS', 'QTI'));
        }
        $this->add($newInteractionResponseProcessing);
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function toQTI(){
        $returnValue = "<responseProcessing>";
        foreach($this->components as $irp){
            $returnValue .= $irp->toQTI();
        }
        $returnValue .= $this->getCompositionQTI();
        $returnValue .= "</responseProcessing>";

        return (string) $returnValue;
    }

    /**
     * Short description of method takeNoticeOfAddedInteraction
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Interaction interaction
     * @param  Item item
     * @return mixed
     */
    public function takeNoticeOfAddedInteraction(taoQTI_models_classes_QTI_interaction_Interaction $interaction, taoQTI_models_classes_QTI_Item $item){
        $irp = taoQTI_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing::create(
                        taoQTI_models_classes_QTI_response_interactionResponseProcessing_MatchCorrectTemplate::CLASS_ID, $interaction->getResponse(), $item
        );
        $this->add($irp);
    }

    /**
     * Short description of method takeNoticeOfRemovedInteraction
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Interaction interaction
     * @param  Item item
     * @return mixed
     */
    public function takeNoticeOfRemovedInteraction(taoQTI_models_classes_QTI_interaction_Interaction $interaction, taoQTI_models_classes_QTI_Item $item){
        $irpExisted = false;
        foreach($this->components as $key => $irp){
            if($irp->getResponse() === $interaction->getResponse()){
                unset($this->components[$key]);
                $irpExisted = true;
                break;
            }
        }
        if(!$irpExisted){
            common_Logger::w('InstanceResponseProcessing not found for removed interaction '.$interaction->getIdentifier(), array('TAOITEMS', 'QTI'));
        }
    }

    /**
     * Short description of method getForm
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Response response
     * @return tao_helpers_form_Form
     */
    public function getForm(taoQTI_models_classes_QTI_ResponseDeclaration $response){
        $formContainer = new taoQTI_actions_QTIform_CompositeResponseOptions($this, $response);
        $returnValue = $formContainer->getForm();

        return $returnValue;
    }

    /**
     * Short description of method getCompositionQTI
     *
     * @abstract
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public abstract function getCompositionQTI();

    /**
     * Short description of method getCompositionRules
     *
     * @abstract
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public abstract function getCompositionRules();
}
/* end of abstract class taoQTI_models_classes_QTI_response_Composite */