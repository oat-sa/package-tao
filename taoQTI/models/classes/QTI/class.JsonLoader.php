<?php
/*
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; under version 2 of the License (non-upgradable). This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA. Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 */

/**
 * Load a QTI item from json format
 *
 * @abstract
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_JsonLoader
{

    protected $item = null;
    protected $data = array();

    public function __construct($json = ''){
        if(is_array($json)){
            $this->data = $json;
        }elseif(is_string($json)){
            $jsonStr = file_exists($json) ? file_get_contents($json) : $json;
            $this->data = json_decode($jsonStr, true);
            if(json_last_error() !== JSON_ERROR_NONE){
                throw new taoQTI_models_classes_QTI_ParsingException('invalid json file or string provided');
            }
        }
    }

    public function load(){
        return $this->buildItem($this->data);
    }

    public function buildItem($data){
        $this->item = new taoQTI_models_classes_QTI_Item($data['attributes']);

        if($this->item instanceof taoQTI_models_classes_QTI_Item){
            if(isset($data['stylesheets'])){
                foreach($data['stylesheets'] as $stylesheet){
                    $this->item->addStylesheet($this->buildElement($stylesheet));
                }
            }
            if(isset($data['namespaces'])){
                foreach($data['namespaces'] as $name => $uri){
                    $this->item->addNamespace($name, $uri);
                }
            }
            if(isset($data['responses'])){
                foreach($data['responses'] as $response){
                    $this->item->addResponse($this->buildResponse($response));
                }
            }
            if(isset($data['outcomes'])){
                $outcomes = array();
                foreach($data['outcomes'] as $outcome){
                    $outcomeDeclaration = $this->buildOutcomeDeclaration($outcome);
                    $outcomes[$outcomeDeclaration->getSerial()] = $outcomeDeclaration;
                }
                $this->item->setOutcomes($outcomes);
            }
        }

        $this->loadContainer($this->item->getBody(), $data['body']);

        return $this->item;
    }

    public function buildResponse($data){

        $response = $this->buildElement($data);

        if($response instanceof taoQTI_models_classes_QTI_ResponseDeclaration){
            if(isset($data['correctResponses'])){
                $response->setCorrectResponses($data['correctResponses']);
            }
            if(isset($data['howMatch'])){
                $response->setHowMatch($data['howMatch']);
            }
            if(isset($data['mapping'])){
                $response->setMapping($data['mapping']);
            }
            if(isset($data['areMapping'])){
                $response->setMapping($data['areMapping'], true);
            }
            if(isset($data['mappingAttributes'])){
                $response->setAttribute('mapping', $data['mappingAttributes']);
            }
        }

        return $response;
    }

    public function buildOutcomeDeclaration($data){

        $outcome = $this->buildElement($data);

        if($outcome instanceof taoQTI_models_classes_QTI_OutcomeDeclaration){
            if(isset($data['defaultValue'])){
                $outcome->setDefaultValue($data['defaultValue']);
            }
        }

        return $outcome;
    }

    protected function buildElement($data, $className = ''){

        $element = null;

        if(is_array($data) && isset($data['type']) && isset($data['serial']) && isset($data['attributes'])){

            $serial = $data['serial'];
            $attributes = $data['attributes'];
            foreach($attributes as $name => $value){
                if(empty($value)){
                    unset($attributes[$name]);
                }
            }
            if(empty($className)){
                $type = $data['type'];
                $className = 'taoQTI_models_classes_QTI_';
                if(stripos($type, 'interaction') !== false){
                    $className .= 'interaction_';
                }elseif(stripos($type, 'choice') !== false || stripos($type, 'hottext') !== false || stripos($type, 'gap') !== false || stripos($type, 'hotspot') !== false){
                    $className .= 'choice_';
                }elseif(stripos($type, 'feedback') !== false){
                    $className .= 'feedback_';
                }
                $className .= ucfirst($type);
            }

            if(class_exists($className)){
                $element = new $className($attributes, $this->item, $serial);
                if($element instanceof taoQTI_models_classes_QTI_container_FlowContainer && isset($data['body'])){
                    $this->loadContainer($element->getBody(), $data['body']);
                }

                if($element instanceof taoQTI_models_classes_QTI_interaction_ObjectInteraction || $element instanceof taoQTI_models_classes_QTI_choice_GapImg){
                    $this->loadObjectData($element->getObject(), $data['object']);
                }

                if($element instanceof taoQTI_models_classes_QTI_interaction_Interaction){
                    $this->loadInteractionData($element, $data);
                }elseif($element instanceof taoQTI_actions_QTIform_choice_Choice){
                    $this->loadChoiceData($element, $data);
                }
            }else{
                throw new taoQTI_models_classes_QTI_ParsingException('the qti element class does not exist: '.$className);
            }
        }else{
            common_Logger::w($data);
            throw new taoQTI_models_classes_QTI_ParsingException('wrong array model');
        }

        return $element;
    }

    protected function loadContainer(taoQTI_models_classes_QTI_container_Container $container, $data){

        if(is_array($data) && isset($data['body'])){
            $body = $data['body'];
            $elts = array();
            if(isset($data['elements']) && is_array($data['elements'])){
                foreach($data['elements'] as $elementData){
                    $elt = $this->buildElement($elementData);
                    if(!is_null($elt)){
                        $elts[$elt->getSerial()] = $elt;
                    }
                }
            }
            $container->setElements($elts, $body);
        }else{
            common_Logger::w($data);
            throw new taoQTI_models_classes_QTI_ParsingException('wrong array model');
        }
    }

    protected function loadInteractionData(taoQTI_models_classes_QTI_interaction_Interaction $interaction, $data){
        if($interaction instanceof taoQTI_models_classes_QTI_interaction_BlockInteraction && isset($data['prompt'])){
            if(isset($data['prompt']['body'])){
                $this->loadContainer($interaction->getPrompt(), $data['prompt']);
            }else{
                throw new taoQTI_models_classes_QTI_ParsingException('no body found from interaction "prompt"');
            }
        }
        $this->buildInteractionChoices($interaction, $data);
    }

    protected function buildInteractionChoices(taoQTI_models_classes_QTI_interaction_Interaction $interaction, $data){

        if(isset($data['choices'])){
            if($interaction instanceof taoQTI_models_classes_QTI_interaction_MatchInteraction){
                for($i = 0; $i < 2; $i++){
                    if(!isset($data['choices'][$i])){
                        throw new taoQTI_models_classes_QTI_ParsingException('missint matchSet for match interaction '.$i);
                    }
                    $matchSet = $data['choices'][$i];
                    foreach($matchSet as $choiceData){
                        $choice = $this->buildElement($choiceData);
                        if(!is_null($choice)){
                            $interaction->addChoice($choice, $i);
                        }
                    }
                }
            }else{
                foreach($data['choices'] as $choiceData){
                    $choice = $this->buildElement($choiceData);
                    if(!is_null($choice)){
                        $interaction->addChoice($choice);
                    }
                }
            }

            if($interaction instanceof taoQTI_models_classes_QTI_interaction_GraphicGapMatchInteraction && isset($data['gapImgs'])){
                foreach($data['gapImgs'] as $gapImgData){
                    $gapImg = $this->buildElement($gapImgData);
                    if(!is_null($gapImg)){
                        $interaction->addGapImg($gapImg);
                    }
                }
            }
        }
    }

    protected function loadChoiceData(taoQTI_models_classes_QTI_choice_Choice $choice, $data){
        if($choice instanceof taoQTI_models_classes_QTI_choice_TextVariableChoice && isset($data['text'])){
            $choice->setContent($data['text']);
        }
    }

    protected function loadObjectData(taoQTI_models_classes_QTI_Object $element, $data){
        
    }

}