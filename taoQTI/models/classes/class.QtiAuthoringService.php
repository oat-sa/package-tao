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
 * Service methods to manage the QTI authoring business
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team
 * @package taoQTI
 * @subpackage models_classes
 */
class taoQTI_models_classes_QtiAuthoringService extends tao_models_classes_GenerisService
{

    /**
     * The RDFS top level item class
     *
     * @access protected
     * @var Class
     */
    protected $itemClass = null;
    protected $qtiService = null;

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     */
    public function __construct(){
        parent::__construct();
        $this->qtiService = taoQTI_models_classes_QTI_Service::singleton();
    }

    /**
     * This method creates a new item object to be used as the data container of the qtiAuthoring tool
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return taoQTI_models_classes_QTI_Item
     */
    public function createNewItem($itemIdentifier = '', $title = ''){


        $returnValue = new taoQTI_models_classes_QTI_Item(array(
            'identifier' => $itemIdentifier,
            'title' => empty($title) ? 'QTI item' : $title
        ));

        //add default responseProcessing:
        $returnValue->setResponseProcessing(taoQTI_models_classes_QTI_response_TemplatesDriven::create($returnValue));

        return $returnValue;
    }

    /**
     * Returns the item data after replacing the interaction tags with the element identifier of the authoring tool
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return taoQTI_models_classes_QTI_Item
     */
    public function getItemData(taoQTI_models_classes_QTI_Item $item){

        $itemData = $this->getFilteredData($item);


        //inserting white spaces to allow easy item selecting:
        $itemData = preg_replace('/}{/', '}&nbsp;{', $itemData);
        $itemData = preg_replace('/^{/', '&nbsp;{', $itemData);
        $itemData = preg_replace('/}$/', '}&nbsp;', $itemData);

        //strip the starting and ending <div> tag if exists:
        $itemData = preg_replace('/^<div>(.*)<\/div>$/ims', '\1', trim($itemData));

        //insert the interaction tags:
        foreach($item->getInteractions() as $interaction){
            $itemData = str_replace($interaction->getPlaceholder(), $this->getInteractionTag($interaction), $itemData);
        }

        $itemData = $this->buildCommonElementTags($item, $itemData);

        return $itemData;
    }

    public function getData(taoQTI_models_classes_QTI_container_FlowContainer $element){
        return $this->buildCommonElementTags($element);
    }

    public function getFilteredData(taoQTI_models_classes_QTI_container_FlowContainer $element){
        $tmpData = $this->getData($element);
        return taoQTI_helpers_qti_ItemAuthoring::filterData($tmpData);
    }

    protected function buildCommonElementTags(taoQTI_models_classes_QTI_container_FlowContainer $element, $bodyData = null){

        if(!is_string($bodyData)){
            $bodyData = $element->getBody()->getBody();
        }
        foreach($element->getBody()->getElements() as $element){
            if($element instanceof taoQTI_models_classes_QTI_Object){
                $bodyData = str_replace($element->getPlaceholder(), $this->getObjectTag($element), $bodyData);
            }else if($element instanceof taoQTI_models_classes_QTI_Math){
                $bodyData = str_replace($element->getPlaceholder(), $this->getMathTag($element), $bodyData);
            }else if($element instanceof taoQTI_models_classes_QTI_interaction_Interaction){
                $bodyData = str_replace($element->getPlaceholder(), $this->getInteractionTag($element), $bodyData);
            }
        }

        return $bodyData;
    }

    public function getMathTag(taoQTI_models_classes_QTI_Math $math){
        $display = ($math->attr('display') == 'block') ? 'block' : 'inline';
        return '{{qtiMath:'.$display.':'.$math->getSerial().'}}';
    }

    public function getObjectTag(taoQTI_models_classes_QTI_Object $pObject){
        return '{{qtiObject:'.$pObject->getSerial().'}}';
    }

    public function getInteractionTag(taoQTI_models_classes_QTI_interaction_Interaction $interaction){
        return '{{qtiInteraction:'.strtolower($interaction->getType()).':'.$interaction->getSerial().'}}';
    }

    /**
     * Get the place holder for hottext editing for hot text interaction only
     * 
     * @param taoQTI_models_classes_QTI_choice_Choice $choice
     * @return string
     */
    public function getChoiceTag(taoQTI_models_classes_QTI_choice_Choice $choice){

        $returnValue = '';

        if($choice instanceof taoQTI_models_classes_QTI_choice_Hottext){
            $choiceData = trim(strip_tags($choice->getContent()));
            $value = (!empty($choiceData)) ? $choiceData : '"empty"';
            $returnValue = '{{qtiHottext:'.$choice->getSerial().':'.$value.'}}';
        }else if($choice instanceof taoQTI_models_classes_QTI_choice_Gap){
            $returnValue = '{{qtiGap:'.$choice->getSerial().':'.$choice->getIdentifier().'}}';
        }

        return $returnValue;
    }

    public function getInteractionData(taoQTI_models_classes_QTI_interaction_Interaction $interaction){

        $data = '';

        if($interaction instanceof taoQTI_models_classes_QTI_container_FlowContainer){
            $data = $interaction->getBody()->getBody(); //filterData()??
            if($interaction instanceof taoQTI_models_classes_QTI_interaction_GapMatchInteraction){
                foreach($interaction->getGaps() as $gap){
                    $data = str_replace($gap->getPlaceholder(), $this->getChoiceTag($gap), $data);
                }
            }else if($interaction instanceof taoQTI_models_classes_QTI_interaction_HottextInteraction){
                foreach($interaction->getChoices() as $gap){
                    $data = str_replace($gap->getPlaceholder(), $this->getChoiceTag($gap), $data);
                }
            }
        }

        return $data;
    }

    //get the choices of a
    private function getChoices(taoQTI_models_classes_QTI_Element $dataObj, $ordered = true){
        //check type interaction or
        if($dataObj instanceof taoQTI_models_classes_QTI_interaction_Interaction || $dataObj instanceof taoQTI_models_classes_QTI_Group){
            
        }
    }

    public function saveItemData(taoQTI_models_classes_QTI_Item $item, $itemBody){
        return $this->saveData($item, '<div>'.$itemBody.'</div>'); //add div because the itemBody is elment only, per qti standard
    }

    public function saveInteractionData(taoQTI_models_classes_QTI_interaction_ContainerInteraction $interaction, $interactionBody){
        return $this->saveData($interaction, $interactionBody);//interaction data must contain blockStatic elements
    }

    public function saveData(taoQTI_models_classes_QTI_container_FlowContainer $element, $body){
        return $element->getBody()->edit($body);
    }

    /**
     * This method creates a new item object to be used as the data container of the qtiAuthoring tool
     *
     * @access public
     * @param  taoQTI_models_classes_QTI_Item item
     * @param  string interactionType
     * @return taoQTI_models_classes_QTI_interaction_Interaction
     */
    public function addInteraction(taoQTI_models_classes_QTI_Item $item, $interactionType, $itemBody){

        $returnValue = null;

        $interactionClass = 'taoQTI_models_classes_QTI_interaction_'.ucfirst($interactionType).'Interaction';

        if(class_exists($interactionClass)){

            $interaction = new $interactionClass(array(), $item);

            //insert the required mandatory non standard default values
            switch(strtolower($interaction->getType())){
                case 'slider':{
                        $interaction->setAttribute('lowerBound', 0.0);
                        $interaction->setAttribute('upperBound', 10.0); //arbitray
                        $interaction->setAttribute('stepLabel', false);
                        $interaction->setAttribute('reverse', false);
                        break;
                    }
                case 'endattempt':{
                        $interaction->setAttribute('title', __('end attempt now'));
                        break;
                    }
            }

            $count = 0;
            $itemBody = str_ireplace('{{qtiInteraction:'.$interactionType.':new}}', $interaction->getPlaceholder(), $itemBody, $count);
            if($count){
                $response = $this->createInteractionResponse($interaction);
                $item->addInteraction($interaction, $itemBody);

                if($interaction instanceof taoQTI_models_classes_QTI_interaction_SelectPointInteraction){
                    $response->setHowMatch(taoQTI_models_classes_QTI_response_Template::MAP_RESPONSE_POINT);
                }

                $returnValue = $interaction;
            }else{
                throw new common_exception_InconsistentData('no placeholder found in the item body for the new interaction');
            }
        }else{
            throw new InvalidArgumentException('The interaction type is not available : '.$interactionType);
        }

        return $returnValue;
    }

    public function addObject(taoQTI_models_classes_QTI_Item $item, $itemBody){

        $returnValue = null;

        $object = new taoQTI_models_classes_QTI_Object();

        $count = 0;
        $itemBody = str_ireplace('{{qtiObject:new}}', $object->getPlaceholder(), $itemBody, $count);
        if($count){
            $item->getBody()->setElement($object, $itemBody);
            $returnValue = $object;
        }else{
            throw new common_exception_InconsistentData('no placeholder found in the item body for the new object');
        }

        return $returnValue;
    }

    /**
     * Prepare data to be saved to the QTI Object model.
     * It replaces custom tags used in the authoring interface by the QTI object actual placeholders
     * 
     * @param taoQTI_models_classes_QTI_Element
     * @param string
     * @access protected
     * @return string
     */
    protected function restorePlaceholder(taoQTI_models_classes_QTI_Element $qtiElement, $data){

        $pattern = '';
        if($qtiElement instanceof taoQTI_models_classes_QTI_interaction_Interaction){
            $pattern = '/{{qtiInteraction:'.$qtiElement->getType().':'.$qtiElement->getSerial().'}}/im';
        }else if($qtiElement instanceof taoQTI_models_classes_QTI_choice_Hottext){
            $pattern = '/{{qtiHottext:'.$qtiElement->getSerial().':([^{]*)}}/im';
        }else if($qtiElement instanceof taoQTI_models_classes_QTI_choice_Gap){
            $pattern = '/{{qtiGap:'.$qtiElement->getSerial().':([^{]*)}}/im';
        }else if($qtiElement instanceof taoQTI_models_classes_QTI_Object){
            $pattern = '/{{qtiObject:'.$qtiElement->getSerial().'}}/im';
        }else if($qtiElement instanceof taoQTI_models_classes_QTI_Math){
            $pattern = '/{{qtiMath:(block|inline):'.$qtiElement->getSerial().'}}/im';
        }

        if(!empty($pattern)){
            $data = preg_replace($pattern, $qtiElement->getPlaceholder(), $data);
        }

        return $data;
    }

    public function restorePlaceholders(taoQTI_models_classes_QTI_container_FlowContainer $qtiElement, $data){
        foreach($qtiElement->getBody()->getElements() as $elt){
            $data = $this->restorePlaceholder($elt, $data);
        }
        return $data;
    }

    public function addElement(taoQTI_models_classes_QTI_container_FlowContainer $elementContainer, $type, $body){

        $returnValue = null;
        switch($type){
            case 'object':
            case 'math':
                $type = ucfirst(strtolower($type));
                break;
            default:
                throw new InvalidArgumentException('unrecognized base qti element type');
        }

        $classname = 'taoQTI_models_classes_QTI_'.$type;
        if(class_exists($classname)){
            $qtiElement = new $classname();
            $count = 0;
            $body = $this->restorePlaceholders($elementContainer, $body);
            $body = str_ireplace('{{qti'.$type.':new}}', $qtiElement->getPlaceholder(), $body, $count);
            if($count){
                if($elementContainer->getBody()->setElement($qtiElement, $body)){
                    $returnValue = $qtiElement;
                }
            }else{
                throw new common_exception_InconsistentData('no placeholder found in the item body for the new interaction');
            }
        }

        return $returnValue;
    }

    public function addMath(taoQTI_models_classes_QTI_Item $item, $itemBody){

        $returnValue = null;

        $math = new taoQTI_models_classes_QTI_Math();

        $count = 0;
        $itemBody = str_ireplace('{{qtiMath:new}}', $math->getPlaceholder(), $itemBody, $count);
        if($count){
            $item->getBody()->setElement($math, $itemBody);
            $returnValue = $math;
        }else{
            throw new common_exception_InconsistentData('no placeholder found in the item body for the new math element');
        }

        return $returnValue;
    }

    public function getInteractionChoiceName($interactionType){

        $returnValue = '';

        switch($interactionType){
            case 'choice':
            case 'order':
            case 'associate':
            case 'match':
            case 'inlinechoice':{
                    $returnValue = 'choice'; //case sensitive! used to get the xml qti element tag + the choice form
                    break;
                }
            case 'gapmatch':{
                    $returnValue = 'gapText';
                    break;
                }
            case 'hottext':{
                    $returnValue = 'hottext';
                    break;
                }
            case 'hotspot':
            case 'graphicorder':
            case 'graphicassociate':{
                    $returnValue = 'hotspot';
                    break;
                }
            case 'graphicgapmatch':{
                    $returnValue = 'gapImg';
                    break;
                }
            default:{
                    throw new InvalidArgumentException('invalid interaction type : '.$interactionType);
                }
        }

        return $returnValue;
    }

    public function createChoice(taoQTI_models_classes_QTI_interaction_Interaction $interaction, $setNumber = null){
        $returnValue = null;
        if(is_null($setNumber)){
            $returnValue = $interaction->createChoice();
        }else if($setNumber === 'gapImg'){
            if($interaction instanceof taoQTI_models_classes_QTI_interaction_GraphicGapMatchInteraction){
                $returnValue = $interaction->createGapImg();
            }else{
                throw new taoQTI_models_classes_QTI_QtiModelException('only graphic gap math interaction can have gapImgs');
            }
        }else if(is_numeric($setNumber) && ($setNumber == 0 || $setNumber == 1)){
            $returnValue = $interaction->createChoice(array(), null, intval($setNumber));
        }
        return $returnValue;
    }

    public function createGap(taoQTI_models_classes_QTI_interaction_Interaction $interaction, $body){

        $returnValue = null;
        $choice = null;
        $count = 0;
        $newGapPlaceholder = '';
        if($interaction instanceof taoQTI_models_classes_QTI_interaction_HottextInteraction){
            $choice = new taoQTI_models_classes_QTI_choice_Hottext();
            $newGapPlaceholder = '{{qtiHottext:new}}';
        }else if($interaction instanceof taoQTI_models_classes_QTI_interaction_GapMatchInteraction){
            $choice = new taoQTI_models_classes_QTI_choice_Gap();
            $newGapPlaceholder = '{{qtiGap:new}}';
        }else{
            throw new InvalidArgumentException('wrong type of interaction');
        }
        if(!is_null($choice)){
            $body = str_replace($newGapPlaceholder, $choice->getPlaceholder(), $body, $count);
            if(!$count){
                throw new common_exception_InconsistentData('no new gap placeholder found');
            }
            if($interaction->getBody()->setElement($choice, $body)){
                $returnValue = $choice;
            }
        }

        return $returnValue;
    }

    public function editChoiceData(taoQTI_models_classes_QTI_choice_Choice $choice, $data = ''){
        if(!is_null($choice)){
            $choice->setdata($data);
        }
    }

    public function deleteInteraction(taoQTI_models_classes_QTI_Item $item, $interaction){

        if(!is_string($interaction) && !$interaction instanceof taoQTI_models_classes_QTI_interaction_Interaction){
            throw new InvalidArgumentException('the interaction must be an instance of taoQTI_models_classes_QTI_interaction_Interaction or a string');
        }
        $interactionSerial = '';
        if($interaction instanceof taoQTI_models_classes_QTI_interaction_Interaction){
            $interactionSerial = $interaction->getSerial();
        }else{
            $interactionSerial = (string) $interaction;
            $interaction = $item->getBody()->getElement($interactionSerial);
        }
        $returnValue = $item->getBody()->removeElement($interactionSerial);

        //delete its response too:
        $response = $interaction->getResponse();
        if(!is_null($response)){
            $item->removeResponse($response);
        }

        //count the number of remaining interactions:
        $interactions = $item->getInteractions();

        if(count($interactions) == 1){
            foreach($interactions as $anInteraction){
                $uniqueResponse = $this->getInteractionResponse($anInteraction);
                // set its response to "RESPONSE":
                if($uniqueResponse->getIdentifier() != 'RESPONSE'){
                    $uniqueResponse->setIdentifier('RESPONSE');
                }
                break;
            }
        }

        return $returnValue;
    }

    public function deleteObject(taoQTI_models_classes_QTI_Item $item, $objectSerial){
        $item->getBody()->removeElement($objectSerial);
    }

    public function deleteChoice(taoQTI_models_classes_QTI_interaction_Interaction $interaction, taoQTI_models_classes_QTI_choice_Choice $choice){

        $interaction->removeChoice($choice); //works for all types of interaction
        //then simulate get+save response data to filter affected response variables
        $this->saveInteractionResponse($interaction, $this->getInteractionResponseData($interaction));

        return true; //@todo add verification here
    }

    public function setOptions(taoQTI_models_classes_QTI_Element $qtiObject, $newOptions = array()){

        if(!is_null($qtiObject) && !empty($newOptions)){

            $options = array();

            foreach($newOptions as $key => $value){
                if(is_array($value)){
                    if(count($value) == 1 && isset($value[0])){

                        if($value[0] !== ''){
                            $options[$key] = $value[0];
                        }
                    }else if(count($value) > 1){
                        $options[$key] = array();
                        foreach($value as $val){

                            if($val !== ''){
                                $options[$key][] = $val;
                            }
                        }
                    }
                }else{
                    if($value !== ''){
                        $options[$key] = $value;
                    }
                }
            }
            $qtiObject->resetAttributes();
            $qtiObject->setAttributes($options);
        }
    }

    public function editOptions(taoQTI_models_classes_QTI_Element $qtiObject, $newOptions = array()){

        if(!is_null($qtiObject) && !empty($newOptions)){
            foreach($newOptions as $key => $value){
                if(is_array($value)){
                    if(count($value) == 1 && isset($value[0])){
                        if($value[0] !== ''){
                            $qtiObject->setAttribute($key, $value[0]);
                        }
                    }else if(count($value) > 1){
                        $values = array();
                        foreach($value as $val){
                            if($val !== ''){
                                $values[] = $val;
                            }
                        }
                        $qtiObject->setAttribute($key, $values);
                    }
                }else{
                    if($value !== ''){
                        $qtiObject->setAttribute($key, $value);
                    }
                }
            }
        }
    }

    public function setPrompt(taoQTI_models_classes_QTI_interaction_BlockInteraction $interaction, $prompt){
        //filter required: strip begining and ending <p> and <div> tags:
        $prompt = $this->restorePlaceholders($interaction->getPromptObject(), $prompt);
        $interaction->setPrompt($prompt);
    }

    public function setChoiceContent(taoQTI_models_classes_QTI_choice_Choice $choice, $content){
        if($choice instanceof taoQTI_models_classes_QTI_choice_ContainerChoice){
            $content = $this->restorePlaceholders($choice, $content);
        }
        $choice->setContent($content);
    }

    public function setIdentifier(taoQTI_models_classes_QTI_Element $qtiObject, $identifier){

        $identifier = preg_replace("/[^a-zA-Z0-9_]{1}/", '', $identifier);
        $oldIdentifier = $qtiObject->getIdentifier();
        if($identifier == $oldIdentifier){
            return true;
        }

        $qtiObject->setIdentifier($identifier);

        //note: taoQTI_models_classes_QTI_Group identifier editable for a "gap" of a gapmatch interaction only
        if($qtiObject instanceof taoQTI_models_classes_QTI_choice_Choice || $qtiObject instanceof taoQTI_models_classes_QTI_Group){

            //update all reference in the response!
            $item = $qtiObject->getRelatedItem();
            if(!is_null($item)){
                $responses = $item->getResponses();
                foreach($responses as $response){
                    $correctResponses = $response->getCorrectResponses();
                    foreach($correctResponses as $key => $choiceConcat){
                        $correctResponses[$key] = preg_replace("/\b{$oldIdentifier}\b/", $identifier, $choiceConcat);
                    }

                    //"normal" mapping only, because of basetype "identifier"
                    $mappings = $response->getMapping();
                    foreach($mappings as $mapping => $score){
                        $count = 0;
                        $newMapping = preg_replace("/\b{$oldIdentifier}\b/", $identifier, $mapping, -1, $count);
                        if($count){
                            unset($mappings[$mapping]);
                            $mappings[$newMapping] = $score;
                        }
                    }

                    $response->setCorrectResponses($correctResponses);
                    $response->setMapping($mappings);
                }
            }else{
                throw new taoQTI_models_classes_QTI_QtiModelException('the choice is not associated to any item');
            }

            return true;
        }

        return false;
    }

    public function setResponseProcessing(taoQTI_models_classes_QTI_Item $item, $type, $customRule = ''){

        $returnValue = false;

        if(!is_null($item)){
            //create a responseProcessing object
            $responseProcessing = null;
            switch(strtolower($type)){
                case 'templatesdriven':{
                        //add a default outcome to work with the reponseProcessing
                        try{
                            $responseProcessing = taoQTI_models_classes_QTI_response_TemplatesDriven::takeOverFrom($item->getResponseProcessing(), $item);
                        }catch(taoQTI_models_classes_QTI_response_TakeoverFailedException $e){
                            $responseProcessing = taoQTI_models_classes_QTI_response_TemplatesDriven::create($item);
                            common_Logger::i('Created new responseProcessign of type '.get_class($responseProcessing));
                        }
                        break;
                    }
                case 'composite':{
                        try{
                            $responseProcessing = taoQTI_models_classes_QTI_response_Composite::takeOverFrom($item->getResponseProcessing(), $item);
                        }catch(taoQTI_models_classes_QTI_response_TakeoverFailedException $e){
                            $responseProcessing = taoQTI_models_classes_QTI_response_Composite::create($item);
                            common_Logger::i('Created new responseProcessing of type '.get_class($responseProcessing));
                        }
                        break;
                    }
                case 'custom':
                case 'customtemplate':
                default:{
                        throw new common_Exception("unavailable response processing type '{$type}'");
                        break;
                    }
            }

            if(!is_null($responseProcessing)){
                $item->setResponseProcessing($responseProcessing); //TODO: destroy from the session the old response processing object?
                $returnValue = true;
            }
        }

        return $returnValue;
    }

    public function getResponseProcessing(taoQTI_models_classes_QTI_Item $item){

        $returnValue = null;

        if(!is_null($item)){
            $returnValue = $item->getResponseProcessing();
        }

        return $returnValue;
    }

    public function getInteractionResponse(taoQTI_models_classes_QTI_interaction_Interaction $interaction){
        $response = $interaction->getResponse();

        if(is_null($response)){
            //create a new one here, with default data model, according to the type of interaction:
            common_Logger::w('interaction '.$interaction->getIdentifier().' is missing a response', array('TAOITEMS', 'QTI'));
            $this->createInteractionResponse($interaction);
        }

        return $response;
    }

    public function createInteractionResponse(taoQTI_models_classes_QTI_interaction_Interaction $interaction){

        $response = new taoQTI_models_classes_QTI_ResponseDeclaration(array(), $interaction->getRelatedItem());
        $response->setIdentifier('RESPONSE', true);
        $interaction->setResponse($response);

        //set the default base type and cardinality to the response:
        if(!$this->updateInteractionResponseOptions($interaction)){
            throw new Exception('the interaction response cannot be updated upon creation');
        }

        return $response;
    }

    public function getInteractionResponseColumnModel(taoQTI_models_classes_QTI_interaction_Interaction $interaction, taoQTI_models_classes_QTI_response_ResponseProcessing $responseProcessing, $isMapping){
        $returnValue = array();
        $interactionType = strtolower($interaction->getType());
        $rowFixed = false;
        switch($interactionType){
            case 'hottext':
                $label = isset($label) ? $label : __('Hottext');
            case 'hotspot':
                $label = isset($label) ? $label : __('Hotspot');
            case 'choice':
            case 'inlinechoice':{
                    $label = isset($label) ? $label : __('Choice');
                    $choices = array();
                    foreach($interaction->getChoices() as $choice){
                        $choices[] = $choice->getIdentifier(); //and not serial, since the identifier is the name that is significant for the user
                    }

                    $i = 1;
                    $editType = 'fixed';
                    $returnValue[] = array(
                        'name' => 'choice'.$i,
                        'label' => $label,
                        'edittype' => $editType,
                        'values' => $choices
                    );
                    $rowFixed = true;
                    break;
                }
            case 'order':
            case 'graphicorder':{
                    $choices = array();
                    foreach($interaction->getChoices() as $choice){
                        $choices[] = $choice->getIdentifier(); //and not serial, since the identifier is the name that is significant to the user
                    }
                    $editType = 'select';
                    for($i = 1; $i <= count($choices); $i++){
                        $returnValue[] = array(
                            'name' => 'choice'.$i,
                            'label' => $i,
                            'edittype' => $editType,
                            'values' => $choices
                        );
                    }
                    break;
                }
            case 'associate':
            case 'graphicassociate':{
                    $choices = array();
                    foreach($interaction->getChoices() as $choice){
                        $choices[] = $choice->getIdentifier(); //and not serial, since the identifier is the name that is significant for the user
                    }
                    $editType = 'select';

                    for($i = 1; $i <= 2; $i++){
                        $returnValue[] = array(
                            'name' => 'choice'.$i,
                            'label' => __('Choice').' '.$i,
                            'edittype' => $editType,
                            'values' => $choices
                        );
                    }

                    break;
                }
            case 'match':{
                    //get groups...
                    $editType = 'select';
                    for($setNumber = 0; $setNumber < 2; $setNumber++){
                        $matchSet = $interaction->getChoices($setNumber);
                        $choices = array();
                        foreach($matchSet as $choice){
                            $choices[] = $choice->getIdentifier();
                        }
                        $i = $setNumber + 1;
                        $returnValue[] = array(
                            'name' => 'choice'.$i,
                            'label' => __('Choice').' '.$i,
                            'edittype' => $editType,
                            'values' => $choices
                        );
                    }
                    break;
                }
            case 'gapmatch':{
                    $groups = array(); //list of gaps
                    foreach($interaction->getGaps() as $gap){
                        $groups[] = $gap->getIdentifier(); //and not serial, since the identifier is the name that is significant for the user
                    }
                    $returnValue[] = $this->getInteractionResponseColumn(1, 'select', $groups, array('label' => __('Gap')));

                    $choices = array(); //list of gapTexts
                    foreach($interaction->getChoices() as $choice){
                        $choices[] = $choice->getIdentifier(); //and not serial, since the identifier is the name that is significant for the user
                    }
                    $returnValue[] = $this->getInteractionResponseColumn(2, 'select', $choices, array('label' => __('Choice')));

                    break;
                }
            case 'graphicgapmatch':{
                    $groups = array(); //list of gaps
                    foreach($interaction->getGapImgs() as $gapImg){
                        $groups[] = $gapImg->getIdentifier(); //and not serial, since the identifier is the name that is significant for the user
                    }
                    $returnValue[] = $this->getInteractionResponseColumn(1, 'select', $groups, array('label' => __('GapImg')));

                    $choices = array(); //list of gapTexts
                    foreach($interaction->getChoices() as $choice){
                        $choices[] = $choice->getIdentifier(); //and not serial, since the identifier is the name that is significant for the user
                    }
                    $returnValue[] = $this->getInteractionResponseColumn(2, 'select', $choices, array('label' => __('Hottspot')));

                    break;
                }
            case 'textentry':
            case 'extendedtext':
            case 'slider':{
                    $returnValue[] = $this->getInteractionResponseColumn(1, 'text');
                    break;
                }
            case 'selectpoint':
            case 'positionobject':{

                    $rpTemplate = $interaction->getResponse()->getHowMatch();
                    if($rpTemplate == taoQTI_models_classes_QTI_response_Template::MAP_RESPONSE_POINT){
                        $selectOptions = array(
                            'point' => 'point',
                            'circle' => 'circle',
                            'ellipse' => 'ellipse',
                            'rect' => 'rect',
                            'poly' => 'poly',
//                            'default' => 'default'
                        );
                    }else{
                        $selectOptions = array(
                            'point' => 'point'
                        );
                    }

                    $returnValue[] = $this->getInteractionResponseColumn(1, 'select', $selectOptions, array('label' => __('Shape'), 'name' => 'shape'));
                    $returnValue[] = $this->getInteractionResponseColumn(2, 'text', null, array('label' => __('Coordinates'), 'name' => 'coordinates'));
                    break;
                }
            default:{
                    throw new Exception("the response column model of the interaction type {$interaction->getType()} is not applicable.");
                    //note: upload and endattempt interactions have no response content
                }
        }

        if($interactionType != 'order' && $interactionType != 'graphicorder'){//no mapping allowed for order interaction for the time being
            //check if the response processing is a match or a map type, or a custom one:
            //correct response (mandatory):
            $returnValue[] = array(
                'name' => 'correct',
                'label' => __('Correct'),
                'edittype' => 'checkbox',
                'values' => array('yes', 'no'),
                'width' => 45
            );

            if($isMapping){
                $returnValue[] = array(
                    'name' => 'score',
                    'label' => __('Score'),
                    'edittype' => 'text',
                    'width' => 60
                );
            }

            if(!$rowFixed){
                $returnValue[] = array(
                    'name' => 'actions',
                    'label' => ' ',
                    'edittype' => 'actions',
                    'width' => 40
                );
            }
        }
        return $returnValue;
    }

    private function getInteractionResponseColumn($index, $editType, $choices = array(), $options = array()){

        $returnValue = array();

        if(intval($index) > 0 && !empty($editType)){

            $returnValue['edittype'] = $editType;

            $name = 'choice'.intval($index);
            if(isset($options['name']) && !empty($options['name'])){
                $name = $options['name'];
            }
            $returnValue['name'] = $name;

            $label = __('Choice').' '.intval($index);
            if(!empty($options)){
                if(isset($options['label'])){
                    $label = $options['label'];
                }
            }
            $returnValue['label'] = $label;

            if(is_array($choices) && !empty($choices)){
                $returnValue['values'] = $choices;
            }
        }

        return $returnValue;
    }

    //is a template or custome, if a template, which one?
    public function getResponseProcessingType(taoQTI_models_classes_QTI_response_ResponseProcessing $responseProcessing = null){
        $returnValue = '';

        if($responseProcessing instanceof taoQTI_models_classes_QTI_response_TemplatesDriven){

            $returnValue = 'templatesdriven';
        }else if($responseProcessing instanceof taoQTI_models_classes_QTI_response_Custom){

            $returnValue = 'custom';
        }else if($responseProcessing instanceof taoQTI_models_classes_QTI_response_Template){

            $returnValue = 'customTemplate';
        }else if($responseProcessing instanceof taoQTI_models_classes_QTI_response_Summation){

            $returnValue = 'summation';
        }else{
            throw new common_Exception('invalid type of response processing: '.get_class($responseProcessing));
        }

        return $returnValue;
    }

    /**
     * find a unique choice within an interaction, based on its identifier
     * 
     * @param taoQTI_models_classes_QTI_interaction_Interaction $interaction
     * @param string $identifier
     * @return taoQTI_models_classes_QTI_choice_Choice
     */
    public function getInteractionChoiceByIdentifier(taoQTI_models_classes_QTI_interaction_Interaction $interaction, $identifier){

        $eltCollection = $interaction->getIdentifiedElements();
        $returnValue = $eltCollection->getUnique($identifier, 'taoQTI_models_classes_QTI_choice_Choice');

        return $returnValue;
    }

    public function saveInteractionResponse(taoQTI_models_classes_QTI_interaction_Interaction $interaction, $responseData){

        $returnValue = false;

        if(!is_null($interaction)){

            $interactionResponse = $this->getInteractionResponse($interaction);

            //sort the key, according to the type of interaction:
            $correctResponses = array();
            $mapping = array();
            $mappingType = '';

            switch(strtolower($interaction->getType())){
                case 'choice':
                case 'inlinechoice':
                case 'hottext':
                case 'extendedtext':
                case 'hotspot':
                case 'textentry':
                case 'slider':{

                        foreach($responseData as $response){
                            $response = (array) $response;
                            //if required identifier not empty:
                            if(!empty($response['choice1'])){

                                $choice1 = trim($response['choice1']);
                                if(!is_null($choice1)){

                                    $responseValue = $choice1;

                                    if($response['correct'] === 'yes' || $response['correct'] === true){
                                        $correctResponses[] = $responseValue;
                                    }

                                    if(isset($response['score'])){
                                        $score = trim($response['score']);
                                        if(is_numeric($score)){
                                            $mapping[$responseValue] = floatval($score);
                                        }
                                    }
                                }
                            }
                        }
                        break;
                    }
                case 'associate':
                case 'match':
                case 'gapmatch':
                case 'graphicassociate':
                case 'graphicgapmatch':{

                        foreach($responseData as $response){
                            $response = (array) $response;
                            if(!empty($response['choice1']) && !empty($response['choice2'])){

                                $choice1 = trim($response['choice1']);
                                $choice2 = trim($response['choice2']);
                                if(!is_null($choice1) && !is_null($choice2)){

                                    $responseValue = $choice1.' '.$choice2;

                                    if($response['correct'] == 'yes' || $response['correct'] === true){
                                        $correctResponses[] = $responseValue;
                                    }
                                    if(isset($response['score'])){
                                        $score = trim($response['score']);
                                        if(is_numeric($score)){
                                            $mapping[$responseValue] = floatval($score);
                                        }
                                    }
                                }
                            }
                        }

                        break;
                    }
                case 'order':
                case 'graphicorder':{
                        foreach($responseData as $response){
                            $response = (array) $response;

                            //find the correct order:
                            $tempResponseValue = array();

                            foreach($response as $choicePosition => $choiceValue){
                                //check if it is a choice:
                                if(strpos($choicePosition, 'choice') === 0){
                                    //ok:
                                    $pos = intval(substr($choicePosition, 6));
                                    if($pos > 0){

                                        $choice = trim($choiceValue);
                                        if(!empty($choice)){
                                            //starting from 1... so need (-1):
                                            $tempResponseValue[$pos - 1] = $choice;
                                        }
                                    }
                                }
                            }

                            //check if order has been breached, i.e. user forgot an intermediate value:
                            if(!empty($tempResponseValue)){
                                $responseValue = array();
                                for($i = 0; $i < count($tempResponseValue); $i++){
                                    if(isset($tempResponseValue[$i])){
                                        $responseValue[$i] = $tempResponseValue[$i];
                                    }else{
                                        break;
                                    }
                                }
                                $correctResponses = $responseValue;
                                $interactionResponse->setCorrectResponses($correctResponses);
                                return true;
                            }
                        }
                        break;
                    }
                case 'selectpoint':
                case 'positionobject':{
                        $mappingType = 'area';
                        foreach($responseData as $response){
                            $response = (array) $response;
                            if(!empty($response['shape']) && !empty($response['coordinates'])){

                                $shape = strtolower(trim($response['shape']));
                                $coordinates = trim($response['coordinates']);
                                if(!is_null($shape) && !is_null($coordinates)){

                                    //shape = point <=> correct = yes
                                    if(($response['correct'] == 'yes' || $response['correct'] === true) || $shape == 'point'){
                                        $coords = explode(',', $coordinates);
                                        if(count($coords) >= 2){
                                            $coords = array_map('trim', $coords);
                                            $correctResponses[] = $coords[0].' '.$coords[1];
                                        }
                                    }else{
                                        $mappingElt = array(
                                            'shape' => $shape,
                                            'coords' => $coordinates
                                        );
                                        if(isset($response['score'])){
                                            $score = trim($response['score']);
                                            if(is_numeric($score)){
                                                $mappingElt['mappedValue'] = floatval($score);
                                            }
                                        }
                                        $mapping[] = $mappingElt;
                                    }
                                }
                            }
                        }
                        break;
                    }
                case 'media':
                case 'endattempt':{
                        //no response to be defined
                        break;
                    }
                default:{
                        throw new common_exception_Error('invalid interaction type for response saving');
                    }
            }

            //set correct responses & mapping
            //note: do not check if empty or not to allow erasing the values
            $interactionResponse->setCorrectResponses($correctResponses);
            $interactionResponse->setMapping($mapping, $mappingType); //method: unsetMapping + unsetCorrectResponses?
            //set the required cardinality and basetype attributes:
            $this->updateInteractionResponseOptions($interaction);

            $returnValue = true;
        }
        return $returnValue;
    }

    public function updateInteractionResponseOptions(taoQTI_models_classes_QTI_interaction_Interaction $interaction){

        $returnValue = false;

        if(!is_null($interaction)){
            $responseOptions = array(
                'cardinality' => $interaction->getCardinality(),
                'baseType' => $interaction->getBaseType()
            );
            $response = $interaction->getResponse();
            if(!is_null($response)){
                $this->editOptions($response, $responseOptions);
                $returnValue = true;
            }
        }

        return $returnValue;
    }

    //correct responses + mapping
    public function getInteractionResponseData(taoQTI_models_classes_QTI_interaction_Interaction $interaction){
        $reponse = $this->getInteractionResponse($interaction);

        $returnValue = array();
        $correctResponses = $reponse->getCorrectResponses();
        $mapping = $reponse->getMapping();
        $maxChoices = $interaction->getCardinality(true);

        $i = 0;
        $interactionType = strtolower($interaction->getType());
        switch($interactionType){
            case 'order':
            case 'graphicorder':{
                    if(!empty($correctResponses)){

                        $returnValue[$i] = array();
                        $returnValue[$i]['correct'] = 'yes';
                        $j = 1;
                        foreach($correctResponses as $choiceIdentifier){
                            $choice = $this->getInteractionChoiceByIdentifier($interaction, $choiceIdentifier);
                            if(is_null($choice)){
                                break; //important: do not take into account deleted choice
                            }
                            $returnValue[$i]["choice{$j}"] = $choiceIdentifier;
                            $j++;
                        }

                        //note: there could only be one correct response so $i should be 0
                        //note 2: there is no possible direct score mapping against correct response order: as a consequence, only the response tlp match can work for the time being
                    }

                    break;
                }
            case 'textentry':
            case 'extendedtext':
            case 'slider':{
                    if(!empty($correctResponses)){
                        foreach($correctResponses as $response){

                            $returnValue[$i] = array(
                                'choice1' => $response,
                                'correct' => 'yes'
                            );

                            if(isset($mapping[$response])){
                                $returnValue[$i]['score'] = $mapping[$response];
                                unset($mapping[$response]);
                            }

                            $i++;

                            //delete exceeding correct responses (0 means infinite)
                            if($maxChoices){
                                if($i >= $maxChoices){
                                    break;
                                }
                            }
                        }
                    }

                    if(!empty($mapping)){
                        foreach($mapping as $response => $score){

                            $returnValue[$i] = array(
                                'choice1' => $response,
                                'correct' => 'no',
                                'score' => $score
                            );

                            $i++;
                        }
                    }

                    break;
                }
            case 'selectpoint':
            case 'positionobject':{

                    if(!empty($correctResponses)){
                        foreach($correctResponses as $response){

                            $response = explode(' ', $response);
                            $response = array_map('trim', $response);
                            if(count($response) == 2){

                                $returnValue[$i] = array(
                                    'shape' => 'point',
                                    'coordinates' => $response[0].', '.$response[1],
                                    'correct' => 'yes'
                                );

                                $i++;

                                //delete exceeding correct responses (0 means infinite)
                                if($maxChoices){
                                    if($i >= $maxChoices){
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    $areaMapping = $reponse->getMapping('area');
                    if(!empty($areaMapping)){
                        foreach($areaMapping as $mapping){

                            $returnValue[$i] = array(
                                'shape' => $mapping['shape'],
                                'coordinates' => $mapping['coords'],
                                'correct' => 'no',
                                'score' => $mapping['mappedValue']
                            );

                            $i++;
                        }
                    }

                    break;
                }
            default:{

                    if(!empty($correctResponses)){
                        foreach($correctResponses as $choiceIdentifierConcat){

                            $choiceIdentifiers = explode(' ', $choiceIdentifierConcat);

                            $returnValue[$i] = array();
                            $returnValue[$i]['correct'] = 'yes';

                            $j = 1; //j<=2
                            //set data as not persistent
                            foreach($choiceIdentifiers as $choiceIdentifier){

                                $choice = $this->getInteractionChoiceByIdentifier($interaction, $choiceIdentifier); //no type check here: could be either a choice or a group
                                if(is_null($choice)){
                                    break(2); //important: do not take into account deleted choice
                                }
                                $returnValue[$i]["choice{$j}"] = $choiceIdentifier;

                                $j++;
                            }

                            if(isset($mapping[$choiceIdentifierConcat])){
                                $returnValue[$i]['score'] = $mapping[$choiceIdentifierConcat];
                                unset($mapping[$choiceIdentifierConcat]);
                            }

                            $i++;

                            if($maxChoices){
                                if($i >= $maxChoices){
                                    break; //delete exceeding correct responses
                                }
                            }
                        }
                    }
                    if(!empty($mapping)){
                        foreach($mapping as $choiceIdentifierConcat => $score){
                            $choiceIdentifiers = explode(' ', $choiceIdentifierConcat);

                            $returnValue[$i] = array();
                            $returnValue[$i]['correct'] = 'no';

                            $j = 1; //j<=2
                            foreach($choiceIdentifiers as $choiceIdentifier){
                                $choice = $this->getInteractionChoiceByIdentifier($interaction, $choiceIdentifier); //no type check: could be either a choice or a group
                                if(is_null($choice)){
                                    break(2); //important: do not take into account deleted choice
                                }
                                $returnValue[$i]["choice{$j}"] = $choiceIdentifier;

                                //add exception for textEntry interaction where the values are the $choiceIdentifier:

                                $j++;
                            }

                            $returnValue[$i]['score'] = $score;

                            $i++;
                        }
                    }
                }
        }

        return $returnValue;
    }

    public function setMappingOptions(taoQTI_models_classes_QTI_ResponseDeclaration $response, $mappingOptions = array()){

        $returnValue = false;

        if(isset($mappingOptions['defaultValue'])){
            $response->setMappingDefaultValue($mappingOptions['defaultValue']);
        }

        $options = array();
        if(isset($mappingOptions['lowerBound'])){
            $value = trim($mappingOptions['lowerBound']);
            if(is_numeric($value)){
                $options['lowerBound'] = floatval($value);
            }
        }
        if(isset($mappingOptions['upperBound'])){
            $value = trim($mappingOptions['upperBound']);
            if(is_numeric($value)){
                $options['upperBound'] = floatval($value);
            }
        }
        $response->setMappingAttributes($options);


        return $returnValue;
    }

    public function isIdentifierUsed(taoQTI_models_classes_QTI_Item $item, $identifier){
        $found = $item->getIdentifiedElement($identifier);
        return !is_null($found);
    }

    public function createFeedbackRule(taoQTI_models_classes_QTI_ResponseDeclaration $response){

        $returnValue = null;

        $relatedItem = $response->getRelatedItem();
        if(!is_null($relatedItem)){
            $outcomeFeedback = new taoQTI_models_classes_QTI_OutcomeDeclaration();
            $relatedItem->addOutcome($outcomeFeedback);
            $outcomeFeedback->setIdentifier('FEEDBACK', true);
            $outcomeFeedback->attr('cardinality', 'single');
            $outcomeFeedback->attr('baseType', 'identifier');

            $feedbackModal = new taoQTI_models_classes_QTI_feedback_ModalFeedback();
            $relatedItem->addModalFeedback($feedbackModal);
            $feedbackModal->setIdentifier('feedbackModal', true);
            $feedbackModal->attr('outcomeIdentifier', $outcomeFeedback);

            $returnValue = new taoQTI_models_classes_QTI_response_SimpleFeedbackRule($outcomeFeedback, $feedbackModal);
            $returnValue->setCondition($response, 'correct');
            $response->addFeedbackRule($returnValue);
        }else{
            throw new taoQTI_models_classes_QTI_QtiModelException('cannot create feedback rule the response is not associated to any item');
        }

        return $returnValue;
    }

    public function deleteFeedbackRule(taoQTI_models_classes_QTI_ResponseDeclaration $response, taoQTI_models_classes_QTI_response_SimpleFeedbackRule $feedbackRule){
        $relatedItem = $feedbackRule->getRelatedItem();
        $relatedItem->removeModalFeedback($feedbackRule->getFeedbackThen());
        $relatedItem->removeOutcome($feedbackRule->getFeedbackOutcome());
        $this->deleteFeedbackRuleElse($feedbackRule);
        return $response->removeFeedbackRule($feedbackRule->getSerial());
    }

    public function addFeedbackRuleElse(taoQTI_models_classes_QTI_response_SimpleFeedbackRule $feedbackRule){

        $returnValue = null;

        $relatedItem = $feedbackRule->getRelatedItem();
        if(!is_null($relatedItem)){

            $feedbackModal = new taoQTI_models_classes_QTI_feedback_ModalFeedback();
            $relatedItem->addModalFeedback($feedbackModal);
            $feedbackModal->setIdentifier('feedbackModal', true);

            $feedbackRule->setFeedbackElse($feedbackModal);
            $feedbackModal->attr('outcomeIdentifier', $feedbackRule->getFeedbackOutcome());
            $returnValue = $feedbackModal;
        }else{
            throw new taoQTI_models_classes_QTI_QtiModelException('cannot create feedback rule else is it is not associated to any item');
        }

        return $returnValue;
    }

    public function deleteFeedbackRuleElse(taoQTI_models_classes_QTI_response_SimpleFeedbackRule $feedbackRule){
        $returnValue = false;
        $feedback = $feedbackRule->getFeedbackElse();
        if(is_null($feedback)){
            $returnValue = true;
        }else{
            $feedbackRule->getRelatedItem()->removeModalFeedback($feedback);
            $returnValue = $feedbackRule->removeFeedbackElse();
        }
        return $returnValue;
    }

}
