<?php
/*
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; under version 2 of the License (non-upgradable). This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA. Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\taoQtiItem\model\qti;

use oat\taoQtiItem\model\qti\JsonLoader;
use oat\taoQtiItem\model\qti\exception\ParsingException;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\ResponseDeclaration;
use oat\taoQtiItem\model\qti\OutcomeDeclaration;
use oat\taoQtiItem\model\qti\container\FlowContainer;
use oat\taoQtiItem\model\qti\interaction\ObjectInteraction;
use oat\taoQtiItem\model\qti\choice\GapImg;
use oat\taoQtiItem\model\qti\interaction\Interaction;
use oat\taoQtiItem\model\qti\container\Container;
use oat\taoQtiItem\model\qti\interaction\BlockInteraction;
use oat\taoQtiItem\model\qti\interaction\MatchInteraction;
use oat\taoQtiItem\model\qti\interaction\GraphicGapMatchInteraction;
use oat\taoQtiItem\model\qti\choice\Choice;
use oat\taoQtiItem\model\qti\choice\TextVariableChoice;
use oat\taoQtiItem\model\qti\Object;
use \common_Logger;

/**
 * Load a QTI item from json format
 *
 * @abstract
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 
 */
class JsonLoader
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
                throw new ParsingException('invalid json file or string provided');
            }
        }
    }

    public function load(){
        return $this->buildItem($this->data);
    }

    public function buildItem($data){
        $this->item = new Item($data['attributes']);

        if($this->item instanceof Item){
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

        if($response instanceof ResponseDeclaration){
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

        if($outcome instanceof OutcomeDeclaration){
            if(isset($data['defaultValue'])){
                $outcome->setDefaultValue($data['defaultValue']);
            }
        }

        return $outcome;
    }

    protected function buildElement($data, $className = ''){

        $element = null;

        if(is_array($data) && isset($data['qtiClass']) && isset($data['serial']) && isset($data['attributes'])){

            $serial = $data['serial'];
            $attributes = $data['attributes'];
            foreach($attributes as $name => $value){
                if(empty($value)){
                    unset($attributes[$name]);
                }
            }
            if(empty($className)){
                $qtiClass = $data['qtiClass'];
                $className = 'oat\\taoQtiItem\\model\\qti\\';
                if(stripos($qtiClass, 'interaction') !== false){
                    $className .= 'interaction\\';
                }elseif(stripos($qtiClass, 'choice') !== false || stripos($qtiClass, 'hottext') !== false || stripos($qtiClass, 'gap') !== false || stripos($qtiClass, 'hotspot') !== false){
                    $className .= 'choice\\';
                }elseif(stripos($qtiClass, 'feedback') !== false){
                    $className .= 'feedback\\';
                }
                $className .= ucfirst($qtiClass);
            }

            if(class_exists($className)){
                $element = new $className($attributes, $this->item, $serial);
                if($element instanceof FlowContainer && isset($data['body'])){
                    $this->loadContainer($element->getBody(), $data['body']);
                }

                if($element instanceof ObjectInteraction || $element instanceof GapImg){
                    $this->loadObjectData($element->getObject(), $data['object']);
                }

                if($element instanceof Interaction){
                    $this->loadInteractionData($element, $data);
                }elseif($element instanceof Choice){
                    $this->loadChoiceData($element, $data);
                }
            }else{
                throw new ParsingException('the qti element class does not exist: '.$className);
            }
        }else{
            common_Logger::w($data);
            throw new ParsingException('wrong array model');
        }

        return $element;
    }

    protected function loadContainer(Container $container, $data){

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
            throw new ParsingException('wrong array model');
        }
    }

    protected function loadInteractionData(Interaction $interaction, $data){
        if($interaction instanceof BlockInteraction && isset($data['prompt'])){
            if(isset($data['prompt']['body'])){
                $this->loadContainer($interaction->getPrompt(), $data['prompt']);
            }else{
                throw new ParsingException('no body found from interaction "prompt"');
            }
        }
        $this->buildInteractionChoices($interaction, $data);
    }

    protected function buildInteractionChoices(Interaction $interaction, $data){

        if(isset($data['choices'])){
            if($interaction instanceof MatchInteraction){
                for($i = 0; $i < 2; $i++){
                    if(!isset($data['choices'][$i])){
                        throw new ParsingException('missint matchSet for match interaction '.$i);
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

            if($interaction instanceof GraphicGapMatchInteraction && isset($data['gapImgs'])){
                foreach($data['gapImgs'] as $gapImgData){
                    $gapImg = $this->buildElement($gapImgData);
                    if(!is_null($gapImg)){
                        $interaction->addGapImg($gapImg);
                    }
                }
            }
        }
    }

    protected function loadChoiceData(Choice $choice, $data){
        if($choice instanceof TextVariableChoice && isset($data['text'])){
            $choice->setContent($data['text']);
        }
    }

    protected function loadObjectData(Object $element, $data){
        
    }

}