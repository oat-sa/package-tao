<?php
/*
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; under version 2 of the License (non-upgradable). This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA. Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 */

/**
 * The QTI_Item object represent the assessmentItem.
 * It's the main QTI object, it contains all the other objects and is the main
 * point
 * to render a complete item.
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#section10042
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_Item extends taoQTI_models_classes_QTI_IdentifiedElement implements taoQTI_models_classes_QTI_container_FlowContainer, taoQTI_models_classes_QTI_IdentifiedElementContainer, common_Serializable
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'assessmentItem';

    /**
     * Item's body content
     *
     * @var taoQTI_models_classes_QTI_container_ContainerInteractive
     */
    protected $body = null;

    /**
     * Item's reponse processing
     *
     * @access protected
     * @var array
     */
    protected $responses = array();

    /**
     * Item's reponse processing
     *
     * @access protected
     * @var ResponseProcessing
     */
    protected $responseProcessing = null;

    /**
     * Item's outcomes
     *
     * @access protected
     * @var array
     */
    protected $outcomes = array();

    /**
     * Item's stylesheets
     *
     * @access protected
     * @var array
     */
    protected $stylesheets = array();

    /**
     * Rubric blocks
     *
     * @access protected
     * @var array
     */
    protected $modalFeedbacks = array();

    /**
     * Rubric blocks
     *
     * @access protected
     * @var array
     */
    protected $rubricBlocks = array();

    /**
     * The namespaces defined in the original qti.xml file,
     * others that the standard included ones
     *
     * @var array
     */
    protected $namespaces = array();

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param
     *            string identifier
     * @param
     *            array options
     * @return mixed
     */
    public function __construct($attributes = array()){
        // override the tool options !
        $attributes['toolName'] = PRODUCT_NAME;
        $attributes['toolVersion'] = TAO_VERSION;

        // create container
        $this->body = new taoQTI_models_classes_QTI_container_ContainerInteractive('', $this);

        parent::__construct($attributes);
    }

    public function addNamespace($name, $uri){
        $this->namespaces[$name] = $uri;
    }

    public function getNamespaces(){
        return $this->namespaces;
    }

    protected function getUsedAttributes(){
        return array(
            'taoQTI_models_classes_QTI_attribute_Title',
            'taoQTI_models_classes_QTI_attribute_Label',
            'taoQTI_models_classes_QTI_attribute_Lang',
            'taoQTI_models_classes_QTI_attribute_Adaptive',
            'taoQTI_models_classes_QTI_attribute_TimeDependent',
            'taoQTI_models_classes_QTI_attribute_ToolName',
            'taoQTI_models_classes_QTI_attribute_ToolVersion'
        );
    }

    public function getBody(){
        return $this->body;
    }

    /**
     * Short description of method addInteraction
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param
     *            Interaction interaction
     * @return mixed
     */
    public function addInteraction(taoQTI_models_classes_QTI_interaction_Interaction $interaction, $body){
        $returnValue = false;

        if(!is_null($interaction)){
            $returnValue = $this->getBody()->setElement($interaction, $body);
        }

        return (bool) $returnValue;
    }

    /**
     * Short description of method removeInteraction
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param
     *            Interaction interaction
     * @return boolean
     */
    public function removeInteraction(taoQTI_models_classes_QTI_interaction_Interaction $interaction){
        $returnValue = false;

        if(!is_null($interaction)){
            $returnValue = $this->getBody()->removeElement($interaction);
        }

        return (bool) $returnValue;
    }

    public function getInteractions(){
        // @todo: make it recursive when adding support of nested interactions
        return $this->body->getElements('taoQTI_models_classes_QTI_interaction_Interaction');
    }

    public function getObjects(){
        // @todo: make it recursive when adding support of nested interactions
        return $this->body->getElements('taoQTI_models_classes_QTI_Object');
    }

    public function getRelatedItem(){
        return $this; // the related item of an item is itself!
    }

    /**
     * Short description of method getResponseProcessing
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return taoQTI_models_classes_QTI_response_ResponseProcessing
     */
    public function getResponseProcessing(){
        return $this->responseProcessing;
    }

    /**
     * Short description of method setResponseProcessing
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param
     *            rprocessing
     * @return mixed
     */
    public function setResponseProcessing($rprocessing){
        $this->responseProcessing = $rprocessing;
    }

    /**
     * Short description of method setOutcomes
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param
     *            array outcomes
     * @return mixed
     */
    public function setOutcomes($outcomes){
        $this->outcomes = array();
        foreach($outcomes as $outcome){
            if(!$outcome instanceof taoQTI_models_classes_QTI_OutcomeDeclaration){
                throw new InvalidArgumentException("wrong entry in outcomes list");
            }
            $this->addOutcome($outcome);
        }
    }

    public function addOutcome(taoQTI_models_classes_QTI_OutcomeDeclaration $outcome){
        $this->outcomes[$outcome->getSerial()] = $outcome;
        $outcome->setRelatedItem($this);
    }

    /**
     * Short description of method getOutcomes
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return array
     */
    public function getOutcomes(){
        return $this->outcomes;
    }

    /**
     * Short description of method getOutcome
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param
     *            string serial
     * @return taoQTI_models_classes_QTI_OutcomeDeclaration
     */
    public function getOutcome($serial){
        $returnValue = null;

        if(!empty($serial)){
            if(isset($this->outcomes[$serial])){
                $returnValue = $this->outcomes[$serial];
            }
        }

        return $returnValue;
    }

    /**
     * Short description of method removeOutcome
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param
     *            Outcome outcome
     * @return boolean
     */
    public function removeOutcome(taoQTI_models_classes_QTI_OutcomeDeclaration $outcome){
        $returnValue = (bool) false;

        if(!is_null($outcome)){
            if(isset($this->outcomes[$outcome->getSerial()])){
                unset($this->outcomes[$outcome->getSerial()]);
                $returnValue = true;
            }
        }else{
            common_Logger::w('Tried to remove null outcome');
        }

        if(!$returnValue){
            common_Logger::w('outcome not found '.$outcome->getSerial());
        }

        return (bool) $returnValue;
    }

    public function addResponse(taoQTI_models_classes_QTI_ResponseDeclaration $response){
        $this->responses[$response->getSerial()] = $response;
        $response->setRelatedItem($this);
    }

    public function getResponses(){
        return $this->responses;
    }

    public function addModalFeedback(taoQTI_models_classes_QTI_feedback_ModalFeedback $modalFeedback){
        $this->modalFeedbacks[$modalFeedback->getSerial()] = $modalFeedback;
        $modalFeedback->setRelatedItem($this);
    }

    public function removeModalFeedback(taoQTI_models_classes_QTI_feedback_ModalFeedback $modalFeedback){
        unset($this->modalFeedbacks[$modalFeedback->getSerial()]);
    }

    /**
     * Get the modal feedbacks of the item
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return array
     */
    public function getModalFeedbacks(){
        return $this->modalFeedbacks;
    }

    public function getModalFeedback($serial){
        $returnValue = null;
        if(isset($this->modalFeedbacks[$serial])){
            $returnValue = $this->modalFeedbacks[$serial];
        }
        return $returnValue;
    }

    /**
     * Get the stylesheets of the item
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return array
     */
    public function getStylesheets(){
        return (array) $this->stylesheets;
    }

    public function addStylesheet(taoQTI_models_classes_QTI_Stylesheet $stylesheet){
        // @todo : validate style sheet before adding:
        $this->stylesheets[$stylesheet->getSerial()] = $stylesheet;
        $stylesheet->setRelatedItem($this);
    }

    public function removeStylesheet(taoQTI_models_classes_QTI_Stylesheet $stylesheet){
        unset($this->stylesheets[$stylesheet->getSerial()]);
    }

    public function removeResponse($response){

        $serial = '';
        if($response instanceof taoQTI_models_classes_QTI_ResponseDeclaration){
            $serial = $response->getSerial();
        }elseif(is_string($response)){
            $serial = $response;
        }else{
            throw new InvalidArgumentException('the argument must be an instance of taoQTI_models_classes_QTI_ResponseDeclaration or a string serial');
        }

        if(!empty($serial)){
            unset($this->responses[$serial]);
        }
    }

    /**
     * Get recursively all included identified QTI elements in the object (identifier => Object)
     *
     * @return array
     */
    public function getIdentifiedElements(){
        $returnValue = $this->getBody()->getIdentifiedElements();
        $returnValue->addMultiple($this->getOutcomes());
        $returnValue->addMultiple($this->getResponses());
        $returnValue->addMultiple($this->getModalFeedbacks());
        return $returnValue;
    }

    /**
     * Short description of method toXHTML
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return string
     */
    public function toXHTML(){

        $template = static::getTemplatePath().'/xhtml.item.tpl.php';
//        $template = static::getTemplatePath().'/xhtml.item.debug.tpl.php';

        // get the variables to use in the template
        $variables = $this->getAttributeValues();
        $variables['stylesheets'] = array();
        foreach($this->getStylesheets() as $stylesheet){
            $variables['stylesheets'][] = $stylesheet->getAttributeValues();
        }

        // these variables enables to get only the needed resources
        $variables['hasUpload'] = false;
        $variables['hasGraphics'] = false;
        $variables['hasSlider'] = false;
        $variables['hasMath'] = false;
        $variables['hasMedia'] = false;
        $variables['useLegacyApi'] = true;
        $variables['clientMatching'] = false;

        //check if specific (and heavy) libs are required:
        $composingElements = $this->getComposingElements();
        foreach($composingElements as $elt){
            if($elt instanceof taoQTI_models_classes_QTI_Math){
                $variables['hasMath'] = true;
                continue;
            }
            if($elt instanceof taoQTI_models_classes_QTI_Object){
                $variables['hasMedia'] = true;
                continue;
            }
            if($elt instanceof taoQTI_models_classes_QTI_interaction_GraphicInteraction){
                $variables['hasGraphics'] = true;
                continue;
            }
            if($elt instanceof taoQTI_models_classes_QTI_interaction_SliderInteraction){
                $variables['hasSlider'] = true;
                continue;
            }
            if($elt instanceof taoQTI_models_classes_QTI_interaction_UploadInteraction){
                $variables['hasUpload'] = true;
                continue;
            }
        }

        $variables['itemData'] = $this->getScoreFreeData();

        if($variables['clientMatching']){
            // get Matching data
            $variables['matchingData'] = $this->getMatchingData();
        }

        $tplRenderer = new taoItems_models_classes_TemplateRenderer($template, $variables);
        $qtifolder = common_ext_ExtensionsManager::singleton()->getExtensionById('taoQTI')->getConstant('BASE_WWW');
        $tplRenderer->setData('ctx_qtiItem_lib_www', $qtifolder.'js/qtiItem/');
        $tplRenderer->setData('ctx_qtiRunner_lib_www', $qtifolder.'js/qtiRunner/');
        $tplRenderer->setData('ctx_qtiDefaultRenderer_lib_www', $qtifolder.'js/qtiDefaultRenderer/');
        $tplRenderer->setData('ctx_math_lib_www', $qtifolder.'js/mathjax/');
        $tplRenderer->setData('ctx_qti_matching_www', $qtifolder.'js/responseProcessing/');

        $taoBaseWww = common_ext_ExtensionsManager::singleton()->getExtensionById('tao')->getConstant('BASE_WWW');
        $itemBaseWww = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems')->getConstant('BASE_WWW');
        $tplRenderer->setData('ctx_taobase_www', $taoBaseWww);
        $tplRenderer->setData('ctx_base_www', $itemBaseWww);
        $tplRenderer->setData('ctx_root_url', ROOT_URL);
        // hide preview button?
        $tplRenderer->setData('ctx_raw_preview', false);
        $tplRenderer->setData('ctx_debug', true);
        // ctx_delivery_server_mode,ctx_matching_server,ctx_base_www,ctx_root_url,ctx_taobase_www,ctx_debug,ctx_raw_preview

        $returnValue = $tplRenderer->render();

        return (string) $returnValue;
    }

    public static function getTemplateQti(){
        return static::getTemplatePath().'/qti.item.tpl.php';
    }

    protected function getTemplateQtiVariables(){
        $variables = parent::getTemplateQtiVariables();

        $variables['stylesheets'] = '';
        foreach($this->stylesheets as $stylesheet){
            $variables['stylesheets'] .= $stylesheet->toQTI();
        }

        $variables['responses'] = '';
        foreach($this->responses as $response){
            $variables['responses'] .= $response->toQTI();
        }

        $variables['outcomes'] = '';
        foreach($this->outcomes as $outcome){
            $variables['outcomes'] .= $outcome->toQTI();
        }

        $variables['feedbacks'] = '';
        foreach($this->modalFeedbacks as $feedback){
            $variables['feedbacks'] .= $feedback->toQTI();
        }

        $namespaces = $this->getNamespaces();
        // remove standard namespaces
        unset($namespaces['']);
        unset($namespaces['xml']);
        unset($namespaces['xsi']);
        $variables['namespaces'] = $namespaces;

        // render the responseProcessing
        $renderedResponseProcessing = '';
        $responseProcessing = $this->getResponseProcessing();
        if(isset($responseProcessing)){
            if($responseProcessing instanceof taoQTI_models_classes_QTI_response_TemplatesDriven){
                $renderedResponseProcessing = $responseProcessing->buildQTI($this);
            }else{
                $renderedResponseProcessing = $responseProcessing->toQTI();
            }
        }

        $variables['renderedResponseProcessing'] = $renderedResponseProcessing;

        return $variables;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return string
     */
    public function toXML(){
        
        $returnValue = '';
        
        $qti = $this->toQTI();

        // render and clean the xml
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $dom->validateOnParse = false;
        if($dom->loadXML($qti)){
            $returnValue = $dom->saveXML();
            
            //in debug mode, systematically check if the save QTI is standard compliant
            if(DEBUG_MODE){
                $parserValidator = new taoQTI_models_classes_QTI_Parser($returnValue);
                $parserValidator->validate();
                if(!$parserValidator->isValid()){
                    common_Logger::w('Invalid QTI output : '.PHP_EOL.' '.$parserValidator->displayErrors());
//                    common_Logger::d(print_r(explode(PHP_EOL, $returnValue),true));
                }
            }
        }else{
            $parserValidator = new taoQTI_models_classes_QTI_Parser($qti);
            $parserValidator->validate();
            if(!$parserValidator->isValid()){
                throw new taoQTI_models_classes_QTI_QtiModelException('Wrong QTI item output format');
            }
        }

        return (string) $returnValue;
    }

    /**
     * Serialize item object into json format, handy to be used in js
     */
    public function toArray(){
        $data = parent::toArray();

        $data['namespaces'] = $this->getNamespaces();

        $data['stylesheets'] = array();
        foreach($this->getStylesheets() as $stylesheet){
            $data['stylesheets'][$stylesheet->getSerial()] = $stylesheet->toArray();
        }

        $data['outcomes'] = array();
        foreach($this->getOutcomes() as $outcome){
            $data['outcomes'][$outcome->getSerial()] = $outcome->toArray();
        }

        $data['responses'] = array();
        foreach($this->getResponses() as $response){
            $data['responses'][$response->getSerial()] = $response->toArray();
        }

        $data['feedbacks'] = array();
        foreach($this->getModalFeedbacks() as $feedback){
            $data['feedbacks'][$feedback->getSerial()] = $feedback->toArray();
        }

        $data['responseProcessing'] = serialize($this->responseProcessing);

        return $data;
    }

    public function getScoreFreeData(){
        $itemData = $this->toArray();
        foreach($itemData['responses'] as $serial => $response){
            //remove anything related to scoring
            unset($itemData['responses'][$serial]['correctResponses']);
            unset($itemData['responses'][$serial]['mapping']);
            unset($itemData['responses'][$serial]['areaMapping']);
            unset($itemData['responses'][$serial]['mappingAttributes']);
            unset($itemData['responses'][$serial]['howMatch']);
        }
        unset($itemData['responseProcessing']);
        return $itemData;
    }

    /**
     * Short description of method getMatchingData
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return array
     */
    public function getMatchingData(){
        $returnValue = array(
            "rule" => null,
            "corrects" => array(),
            "maps" => array(),
            "areaMaps" => array(),
            "outcomes" => array()
        );

        // BUILD the RP rule
        if(!is_null($this->getResponseProcessing())){
            if($this->getResponseProcessing() instanceof taoQTI_models_classes_QTI_response_TemplatesDriven){
                $returnValue["rule"] = $this->getResponseProcessing()->buildRule($this);
            }else{
                $returnValue["rule"] = $this->getResponseProcessing()->getRule();
            }
        }

        // Get the correct responses (correct variables and map variables)
        $corrects = array();
        $maps = array();
        $interactions = $this->getInteractions();
        foreach($interactions as $interaction){
            if($interaction->getResponse() != null){
                $correctJSON = $interaction->getResponse()->correctToJSON();
                if($correctJSON != null){
                    array_push($returnValue["corrects"], $correctJSON);
                }

                $mapJson = $interaction->getResponse()->mapToJSON();
                if($mapJson != null){
                    array_push($returnValue["maps"], $mapJson);
                }

                $areaMapJson = $interaction->getResponse()->areaMapToJSON();
                if($areaMapJson != null){
                    array_push($returnValue["areaMaps"], $areaMapJson);
                }
            }
        }

        // Get the outcome variables
        $outcomes = $this->getOutcomes();
        foreach($outcomes as $outcome){
            array_push($returnValue["outcomes"], $outcome->toJSON());
        }

        return (array) $returnValue;
    }

    public function toForm(){

        $formContainer = new taoQTI_actions_QTIform_AssessmentItem($this);
        $returnValue = $formContainer->getForm();

        return $returnValue;
    }

}
/* end of class taoQTI_models_classes_QTI_Item */