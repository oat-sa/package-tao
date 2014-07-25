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

class taoQTI_models_classes_QTI_response_SimpleFeedbackRule extends taoQTI_models_classes_QTI_Element
{

    protected $condition = 'correct'; //lt, lte, equal, gte, gt
    protected $comparedOutcome = null;
    protected $comparedValue = 0.0; //value to be compared with, required is condition is different from correct
    protected $feedbackThen = null;
    protected $feedbackElse = null;
    protected $feedbackOutcome = null;

    public function __construct(taoQTI_models_classes_QTI_OutcomeDeclaration $feedbackOutcome, $feedbackThen = null, $feedbackElse = null){
        $this->feedbackOutcome = $feedbackOutcome;
        if(!is_null($feedbackThen)){
            $this->setFeedbackThen($feedbackThen);
        }
        if(!is_null($feedbackElse)){
            $this->setFeedbackElse($feedbackElse);
        }
        $this->getSerial();
    }

    public function getUsedAttributes(){
        return array();
    }

    public function getFeedbackOutcome(){
        return $this->feedbackOutcome;
    }

    public function comparedOutcome(){
        return $this->comparedOutcome;
    }

    public function getFeedbackThen(){
        return $this->feedbackThen;
    }

    public function getFeedbackElse(){
        return $this->feedbackElse;
    }

    public function getCondition(){
        return $this->condition;
    }

    public function setFeedbackThen(taoQTI_models_classes_QTI_feedback_Feedback $feedback){
        $this->feedbackThen = $feedback;
    }

    public function removeFeedbackElse(){
        $this->feedbackElse = null;
        return true;
    }

    public function setFeedbackElse(taoQTI_models_classes_QTI_feedback_Feedback $feedback){
        $this->feedbackElse = $feedback;
    }

    public function setCondition(taoQTI_models_classes_QTI_VariableDeclaration $comparedOutcome, $condition, $comparedValue = null){

        $returnValue = false;

        switch($condition){
            case 'correct':{
                    if($comparedOutcome instanceof taoQTI_models_classes_QTI_ResponseDeclaration){
                        $this->comparedOutcome = $comparedOutcome;
                        $this->condition = $condition;
                        //we may leave the comparedValue current default (if not nul) if we would like to switch back to another condition
                        $returnValue = true;
                    }else{
                        throw new InvalidArgumentException('compared outcome must be a response for correct condition');
                    }
                    break;
                }
            case 'lt':
            case 'lte':
            case 'equal':
            case 'gte':
            case 'gt':{
                    if(!is_null($comparedValue)){
                        $this->comparedOutcome = $comparedOutcome;
                        $this->condition = $condition;
                        $this->comparedValue = $comparedValue;
                        $returnValue = true;
                    }else{
                        throw new InvalidArgumentException('compared value must not be null');
                    }
                }
        }

        return $returnValue;
    }

    public function toArray(){

        $data = array(
            'serial' => $this->getSerial(),
            'comparedOutcome' => is_null($this->comparedOutcome) ? '' : $this->comparedOutcome->getSerial(),
            'comparedValue' => $this->comparedValue,
            'condition' => $this->condition,
            'feedbackThen' => is_null($this->feedbackThen) ? '' : $this->feedbackThen->getSerial(),
            'feedbackElse' => is_null($this->feedbackElse) ? '' : $this->feedbackElse->getSerial()
        );

        return $data;
    }

    public function toQTI(){

        $tplPath = ROOT_PATH.'taoQTI/models/classes/QTI/templates/feedbacks/rules/';

        $variables = array();
        $variables['feedbackOutcomeIdentifier'] = $this->feedbackOutcome->getIdentifier();
        $variables['feedbackIdentifierThen'] = $this->feedbackThen->getIdentifier();
        $variables['feedbackIdentifierElse'] = is_null($this->feedbackElse) ? null : $this->feedbackElse->getIdentifier();

        if($this->condition == 'correct'){
            $tpl = 'qti.correct.tpl.php';
            //the response processing tpl does not need to be CORRECT to allow condition to be correct
            $variables['responseIdentifier'] = $this->comparedOutcome->getIdentifier();
        }else{
            $tpl = 'qti.condition.tpl.php';
            if($this->comparedOutcome instanceof taoQTI_models_classes_QTI_ResponseDeclaration){
                $response = $this->comparedOutcome;
                $variables['responseIdentifier'] = $response->getIdentifier();
                switch($response->getHowMatch()){
                    case taoQTI_models_classes_QTI_response_Template::MAP_RESPONSE:{
                            $variables['map'] = true;
                            $variables['mapPoint'] = false;
                            break;
                        }
                    case taoQTI_models_classes_QTI_response_Template::MAP_RESPONSE_POINT:{
                            $variables['mapPoint'] = true;
                            $variables['map'] = false;
                            break;
                        }
                    case taoQTI_models_classes_QTI_response_Template::MATCH_CORRECT:{
                            $variables['map'] = true;
                            $variables['mapPoint'] = false;
                            //allow loose control: assume simple response mapping 'MAP_RESPONSE' for beedback rule evaluation
//                            throw new common_Exception('condition needs to be set to correct for match correct');
                            break;
                        }
                }
            }else{
                $variables['outcomeIdentifier'] = $this->comparedOutcome->getIdentifier();
            }

            $variables['condition'] = $this->condition;
            $variables['value'] = $this->comparedValue;
        }

        $tplRenderer = new taoItems_models_classes_TemplateRenderer($tplPath.$tpl, $variables);

        $returnValue = $tplRenderer->render();

        return (string) $returnValue;
    }

}