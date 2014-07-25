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
 * @subpackage models_classes_QTI_response
 */

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_response
 */
class taoQTI_models_classes_QTI_response_TemplatesDriven extends taoQTI_models_classes_QTI_response_ResponseProcessing implements taoQTI_models_classes_QTI_response_Rule
{

    /**
     * Short description of method getRule
     *
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
            taoQTI_models_classes_QTI_response_Template::MATCH_CORRECT,
            taoQTI_models_classes_QTI_response_Template::MAP_RESPONSE,
            taoQTI_models_classes_QTI_response_Template::MAP_RESPONSE_POINT
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
     * @return taoQTI_models_classes_QTI_response_ResponseProcessing
     */
    public static function create(taoQTI_models_classes_QTI_Item $item){
        $returnValue = new taoQTI_models_classes_QTI_response_TemplatesDriven();
        if(count($item->getOutcomes()) == 0){
            $item->setOutcomes(array(
                new taoQTI_models_classes_QTI_OutcomeDeclaration(array('identifier' => 'SCORE', 'baseType' => 'float', 'cardinality' => 'single'))
            ));
        }
        foreach($item->getInteractions() as $interaction){
            $returnValue->setTemplate($interaction->getResponse(), taoQTI_models_classes_QTI_response_Template::MATCH_CORRECT);
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
     * @return taoQTI_models_classes_QTI_response_ResponseProcessing
     */
    public static function takeOverFrom(taoQTI_models_classes_QTI_response_ResponseProcessing $responseProcessing, taoQTI_models_classes_QTI_Item $item){
        $returnValue = null;

        if($responseProcessing instanceof self){
            return $responseProcessing;
        }

        if($responseProcessing instanceof taoQTI_models_classes_QTI_response_Template){
            $returnValue = new taoQTI_models_classes_QTI_response_TemplatesDriven();
            // theoretic only interaction 'RESPONSE' should be considered
            foreach($item->getInteractions() as $interaction){
                $returnValue->setTemplate($interaction->getResponse(), $responseProcessing->getUri());
            }
        }else{
            throw new taoQTI_models_classes_QTI_response_TakeoverFailedException();
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
    public function setTemplate(taoQTI_models_classes_QTI_ResponseDeclaration $response, $template){

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
    public function getTemplate(taoQTI_models_classes_QTI_ResponseDeclaration $response){
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
    public function takeNoticeOfAddedInteraction(taoQTI_models_classes_QTI_interaction_Interaction $interaction, taoQTI_models_classes_QTI_Item $item){
        $interaction->getResponse()->setHowMatch(taoQTI_models_classes_QTI_response_Template::MATCH_CORRECT);
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
        $formContainer = new taoQTI_actions_QTIform_TemplatesDrivenResponseOptions($this, $response);
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
    public function buildQTI(taoQTI_models_classes_QTI_Item $item){
        
        $interactions = $item->getInteractions();
        if(count($interactions) == 1){
            foreach($item->getInteractions() as $interaction){
                $response = $interaction->getResponse();
                if(count($response->getFeedbackRules())){
                    break;//need to output feedback rules
                }else{
                    $uri = $response->getHowMatch();
                    $responseProcessingToRender = new taoQTI_models_classes_QTI_response_Template($uri);
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
    public function buildRule(taoQTI_models_classes_QTI_Item $item){
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
          $responseProcessingToRender = new taoQTI_models_classes_QTI_response_Template($uri);
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

    public function toArray(){
        return array();
    }

    protected function getUsedAttributes(){
        return array();
    }

}