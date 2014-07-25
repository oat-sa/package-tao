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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti\response;

use oat\taoQtiItem\model\qti\response\TemplatesDriven;
use oat\taoQtiItem\model\qti\response\ResponseProcessing;
use oat\taoQtiItem\model\qti\response\Rule;
use oat\taoQtiItem\model\qti\response\Template;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\OutcomeDeclaration;
use oat\taoQtiItem\model\qti\response\TakeoverFailedException;
use oat\taoQtiItem\model\qti\ResponseDeclaration;
use oat\taoQtiItem\model\qti\interaction\Interaction;
use oat\taoQtiItem\controller\QTIform\TemplatesDrivenResponseOptions;
use \common_exception_Error;
use \taoItems_models_classes_TemplateRenderer;

/**
 * TAO - taoQTI/models/classes/QTI/response/class.TemplatesDriven.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 09.02.2012, 16:25:40 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
class TemplatesDriven extends ResponseProcessing implements Rule
{

    /**
     * Short description of method getRule
     *
     * @deprecated now using new qtism lib for response processing
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRule(){
        $returnValue = (string) '';

        throw new common_exception_Error('please use buildRule for templateDriven instead');

        return (string) $returnValue;
    }

    /**
     * Short description of method isSupportedTemplate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string uri
     * @return taoQTI_models_classes_Matching_bool
     */
    public static function isSupportedTemplate($uri){

        $mythoMap = Array(
            Template::MATCH_CORRECT,
            Template::MAP_RESPONSE,
            Template::MAP_RESPONSE_POINT
        );

        $returnValue = in_array($uri, $mythoMap);

        return (bool) $returnValue;
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
        $returnValue = new TemplatesDriven();
        if(count($item->getOutcomes()) == 0){
            $item->setOutcomes(array(
                new OutcomeDeclaration(array('identifier' => 'SCORE', 'baseType' => 'float', 'cardinality' => 'single'))
            ));
        }
        foreach($item->getInteractions() as $interaction){
            $returnValue->setTemplate($interaction->getResponse(), Template::MATCH_CORRECT);
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
     * @return oat\taoQtiItem\model\qti\response\ResponseProcessing
     */
    public static function takeOverFrom(ResponseProcessing $responseProcessing, Item $item){
        $returnValue = null;

        if($responseProcessing instanceof self){
            return $responseProcessing;
        }

        if($responseProcessing instanceof Template){
            $returnValue = new TemplatesDriven();
            // theoretic only interaction 'RESPONSE' should be considered
            foreach($item->getInteractions() as $interaction){
                $returnValue->setTemplate($interaction->getResponse(), $responseProcessing->getUri());
            }
        }else{
            throw new TakeoverFailedException();
        }

        return $returnValue;
    }

    /**
     * Short description of method setTemplate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Response response
     * @param  string template
     * @return boolean
     */
    public function setTemplate(ResponseDeclaration $response, $template){

        $response->setHowMatch($template);
        $returnValue = true;

        return (bool) $returnValue;
    }

    /**
     * Short description of method getTemplate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Response response
     * @return string
     */
    public function getTemplate(ResponseDeclaration $response){
        return (string) $response->getHowMatch();
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
        $interaction->getResponse()->setHowMatch(Template::MATCH_CORRECT);
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
        $formContainer = new TemplatesDrivenResponseOptions($this, $response);
        $returnValue = $formContainer->getForm();

        return $returnValue;
    }

    /**
     * Short description of method buildQTI
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @return string
     */
    public function buildQTI(Item $item){
        
        $interactions = $item->getInteractions();
        if(count($interactions) == 1){
            foreach($item->getInteractions() as $interaction){
                $response = $interaction->getResponse();
                if(count($response->getFeedbackRules())){
                    break;//need to output feedback rules
                }else{
                    $uri = $response->getHowMatch();
                    $responseProcessingToRender = new Template($uri);
                    return $responseProcessingToRender->toQTI();
                }
            }
        }
        
        $returnValue = "<responseProcessing>";
        foreach($interactions as $interaction){
            $response = $interaction->getResponse();
            $uri = $response->getHowMatch();
            $templateName = substr($uri, strrpos($uri, '/') + 1);
            $matchingTemplate = dirname(__FILE__).'/rpTemplate/qti.'.$templateName.'.tpl.php';

            $tplRenderer = new taoItems_models_classes_TemplateRenderer($matchingTemplate, Array(
                'responseIdentifier' => $response->getIdentifier()
                , 'outcomeIdentifier' => 'SCORE'
            ));
            $returnValue .= $tplRenderer->render();

            //add simple feedback rules:
            foreach($response->getFeedbackRules() as $rule){
                $returnValue .= $rule->toQTI();
            }
        }
        $returnValue .= "</responseProcessing>";

        return (string) $returnValue;
    }

    /**
     * Short description of method buildRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @return string
     */
    public function buildRule(Item $item){
        $returnValue = (string) '';

        foreach($item->getInteractions() as $interaction){
            $response = $interaction->getResponse();
            $uri = $response->getHowMatch();
            $templateName = substr($uri, strrpos($uri, '/') + 1);
            $matchingTemplate = dirname(__FILE__).'/rpTemplate/rule.'.$templateName.'.tpl.php';

            $tplRenderer = new taoItems_models_classes_TemplateRenderer(
                    $matchingTemplate, Array('responseIdentifier' => $response->getIdentifier(), 'outcomeIdentifier' => 'SCORE')
            );
            $returnValue .= $tplRenderer->render();
        }

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function toQTI(){
        $returnValue = (string) '';

        throw new common_exception_Error('please use buildQTI for templateDriven instead');
        /*
          if (count($this->templateMap) == 1) {
          foreach($this->templateMap as $uri){
          $responseProcessingToRender = new oat\taoQtiItem\model\qti\response\Template($uri);
          $returnValue = $responseProcessingToRender->toQTI();
          }
          } else {
          $returnValue = "<responseProcessing>";
          foreach ($this->templateMap as $identifier => $templateName) {
          $returnValue .= $this->buildQTI($templateName, Array(
          'responseIdentifier'=> $identifier
          , 'outcomeIdentifier'=>'SCORE'
          ));
          }
          $returnValue .= "</responseProcessing>";
          }
         */

        return (string) $returnValue;
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){
        
        $returnValue = parent::toArray($filterVariableContent, $filtered);
        
        $protectedData = array(
            'processingType' => 'templateDriven'
        );
        
        if($filterVariableContent){
            $filtered[$this->getSerial()] = $protectedData;
        }else{
            $returnValue = array_merge($returnValue, $protectedData);
        }
        
        return $returnValue;
    }

    protected function getUsedAttributes(){
        return array();
    }

}