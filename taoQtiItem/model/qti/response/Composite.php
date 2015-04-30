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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti\response;

use oat\taoQtiItem\model\qti\response\Composite;
use oat\taoQtiItem\model\qti\response\ResponseProcessing;
use oat\taoQtiItem\model\qti\response\Rule;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\OutcomeDeclaration;
use oat\taoQtiItem\model\qti\response\Summation;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\InteractionResponseProcessing;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\None;
use oat\taoQtiItem\model\qti\response\Template;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\Template;
use oat\taoQtiItem\model\qti\response\TakeoverFailedException;
use oat\taoQtiItem\model\qti\response\TemplatesDriven;
use oat\taoQtiItem\model\qti\ResponseDeclaration;
use oat\taoQtiItem\model\qti\interaction\Interaction;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\MatchCorrectTemplate;
use oat\taoQtiItem\controller\QTIform\CompositeResponseOptions;
use \Exception;
use \common_Logger;
use \common_Exception;

/**
 * Short description of class oat\taoQtiItem\model\qti\response\Composite
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
abstract class Composite extends ResponseProcessing implements Rule
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
    public function __construct(Item $item, $outcomeIdentifier = 'SCORE'){
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
            $outcomes[] = new OutcomeDeclaration(array('identifier' => $outcomeIdentifier, 'baseType' => 'float', 'cardinality' => 'single'));
            $item->setOutcomes($outcomes);
        }
    }

    /**
     * Short description of method create
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @return oat\taoQtiItem\model\qti\response\ResponseProcessing
     */
    public static function create(Item $item){
        $returnValue = new Summation($item);
        foreach($item->getInteractions() as $interaction){
            $irp = InteractionResponseProcessing::create(
                            None::CLASS_ID
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
     * @return oat\taoQtiItem\model\qti\response\Composite
     */
    public static function takeOverFrom(ResponseProcessing $responseProcessing, Item $item){
        $returnValue = null;

        if($responseProcessing instanceof static){
            // already good
            $returnValue = $responseProcessing;
        }elseif($responseProcessing instanceof Template){
            // IMS Template
            $rp = new Summation($item, 'SCORE');
            foreach($item->getInteractions() as $interaction){
                $response = $interaction->getResponse();
                try{
                    $irp = Template::createByTemplate(
                                    $responseProcessing->getUri(), $response, $item);
                }catch(Exception $e){
                    throw new TakeoverFailedException();
                }
                $rp->add($irp, $item);
            }
            $returnValue = $rp;
        }elseif($responseProcessing instanceof TemplatesDriven){
            // TemplateDriven
            $rp = new Summation($item, 'SCORE');
            foreach($item->getInteractions() as $interaction){
                $response = $interaction->getResponse();
                try{
                    $irp = Template::createByTemplate(
                                    $responseProcessing->getTemplate($response)
                                    , $response
                                    , $item
                    );
                }catch(Exception $e){
                    throw new TakeoverFailedException();
                }
                $rp->add($irp, $item);
            }
            $returnValue = $rp;
        }else{
            common_Logger::d('Composite ResponseProcessing can not takeover from '.get_class($responseProcessing).' yet');
            throw new TakeoverFailedException();
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
    public function add(InteractionResponseProcessing $interactionResponseProcessing){
        $this->components[] = $interactionResponseProcessing;
    }

    /**
     * Short description of method getInteractionResponseProcessing
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Response response
     * @return oat\taoQtiItem\model\qti\response\interactionResponseProcessing\InteractionResponseProcessing
     */
    public function getInteractionResponseProcessing(ResponseDeclaration $response){
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
     * @return oat\taoQtiItem\model\qti\response\interactionResponseProcessing\InteractionResponseProcessing
     */
    public function getIRPByOutcome(OutcomeDeclaration $outcome){
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
    public function replace(InteractionResponseProcessing $newInteractionResponseProcessing){
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
    public function takeNoticeOfAddedInteraction(Interaction $interaction, Item $item){
        $irp = InteractionResponseProcessing::create(
                        MatchCorrectTemplate::CLASS_ID, $interaction->getResponse(), $item
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
    public function takeNoticeOfRemovedInteraction(Interaction $interaction, Item $item){
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
    public function getForm(ResponseDeclaration $response){
        $formContainer = new CompositeResponseOptions($this, $response);
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