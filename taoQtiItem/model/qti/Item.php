<?php
/*
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; under version 2 of the License (non-upgradable). This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA. Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\taoQtiItem\model\qti;

use oat\taoQtiItem\model\qti\container\FlowContainer;
use oat\taoQtiItem\model\qti\container\ContainerItemBody;
use oat\taoQtiItem\model\qti\interaction\Interaction;
use oat\taoQtiItem\model\qti\feedback\ModalFeedback;
use oat\taoQtiItem\model\qti\response\TemplatesDriven;
use oat\taoQtiItem\model\qti\exception\QtiModelException;
use oat\taoQtiItem\controller\QTIform\AssessmentItem;
use \common_Serializable;
use \common_Logger;
use \taoItems_models_classes_TemplateRenderer;
use \DOMDocument;
use oat\tao\helpers\Template;

/**
 * The QTI_Item object represent the assessmentItem.
 * It's the main QTI object, it contains all the other objects and
 * is the main point to render a complete item.
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#section10042
 */
class Item extends IdentifiedElement implements FlowContainer, IdentifiedElementContainer, common_Serializable
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
     * @var \oat\taoQtiItem\model\qti\container\ContainerItemBody
     */
    protected $body = null;

    /**
     * Item's response processing
     *
     * @access protected
     * @var array
     */
    protected $responses = array();

    /**
     * Item's response processing
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
     * @param array $attributes
     * @return mixed
     */
    public function __construct($attributes = array()){
        // override the tool options !
        $attributes['toolName'] = PRODUCT_NAME;
        $attributes['toolVersion'] = TAO_VERSION;

        // create container
        $this->body = new ContainerItemBody('', $this);

        parent::__construct($attributes);
    }

    public function addNamespace($name, $uri){
        $this->namespaces[$name] = $uri;
    }

    public function getNamespaces(){
        return $this->namespaces;
    }

    public function getNamespace($uri){
        return array_search($uri, $this->namespaces);
    }

    protected function getUsedAttributes(){
        return array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\Title',
            'oat\\taoQtiItem\\model\\qti\\attribute\\Label',
            'oat\\taoQtiItem\\model\\qti\\attribute\\Lang',
            'oat\\taoQtiItem\\model\\qti\\attribute\\Adaptive',
            'oat\\taoQtiItem\\model\\qti\\attribute\\TimeDependent',
            'oat\\taoQtiItem\\model\\qti\\attribute\\ToolName',
            'oat\\taoQtiItem\\model\\qti\\attribute\\ToolVersion'
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
     * @param Interaction $interaction
     * @param $body
     * @return bool
     */
    public function addInteraction(Interaction $interaction, $body){
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
     * @param Interaction $interaction
     * @return bool
     */
    public function removeInteraction(Interaction $interaction){
        $returnValue = false;

        if(!is_null($interaction)){
            $returnValue = $this->getBody()->removeElement($interaction);
        }

        return (bool) $returnValue;
    }

    public function getInteractions(){
        // @todo: make it recursive when adding support of nested interactions
        return $this->body->getElements('oat\\taoQtiItem\\model\\qti\\interaction\\Interaction');
    }

    public function getObjects(){
        return $this->body->getElements('oat\\taoQtiItem\\model\\qti\\Object');
    }

    public function getRubricBlocks(){
        return $this->body->getElements('oat\\taoQtiItem\\model\\qti\\RubricBlock');
    }

    public function getRelatedItem(){
        return $this; // the related item of an item is itself!
    }

    /**
     * Short description of method getResponseProcessing
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return \oat\taoQtiItem\model\qti\response\ResponseProcessing
     */
    public function getResponseProcessing(){
        return $this->responseProcessing;
    }

    /**
     * Short description of method setResponseProcessing
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param $rprocessing
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
     * @throws InvalidArgumentException
     */
    public function setOutcomes($outcomes){
        $this->outcomes = array();
        foreach($outcomes as $outcome){
            if(!$outcome instanceof OutcomeDeclaration){
                throw new InvalidArgumentException("wrong entry in outcomes list");
            }
            $this->addOutcome($outcome);
        }
    }

    /**
     * add an outcome declaration to the item
     * 
     * @param \oat\taoQtiItem\model\qti\OutcomeDeclaration $outcome
     */
    public function addOutcome(OutcomeDeclaration $outcome){
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
     * @return \oat\taoQtiItem\model\qti\OutcomeDeclaration
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
     * @param OutcomeDeclaration $outcome
     * @return bool
     */
    public function removeOutcome(OutcomeDeclaration $outcome){
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

    public function addResponse(ResponseDeclaration $response){
        $this->responses[$response->getSerial()] = $response;
        $response->setRelatedItem($this);
    }

    public function getResponses(){
        return $this->responses;
    }

    public function addModalFeedback(ModalFeedback $modalFeedback){
        $this->modalFeedbacks[$modalFeedback->getSerial()] = $modalFeedback;
        $modalFeedback->setRelatedItem($this);
    }

    public function removeModalFeedback(ModalFeedback $modalFeedback){
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

    public function addStylesheet(Stylesheet $stylesheet){
        // @todo : validate style sheet before adding:
        $this->stylesheets[$stylesheet->getSerial()] = $stylesheet;
        $stylesheet->setRelatedItem($this);
    }

    public function removeStylesheet(Stylesheet $stylesheet){
        unset($this->stylesheets[$stylesheet->getSerial()]);
    }

    public function removeResponse($response){

        $serial = '';
        if($response instanceof ResponseDeclaration){
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
     * @param array $options
     * @param array $filtered
     * @return string
     */
    public function toXHTML($options = array(), &$filtered = array()){

        $template = static::getTemplatePath().'/xhtml.item.tpl.php';

        // get the variables to use in the template
        $variables = $this->getAttributeValues();
        $variables['stylesheets'] = array();
        foreach($this->getStylesheets() as $stylesheet){
            $variables['stylesheets'][] = $stylesheet->getAttributeValues();
        }
        //additional css:
        if(isset($options['css'])){
            foreach($options['css'] as $css){
                $variables['stylesheets'][] = array('href' => $css, 'media' => 'all');
            }
        }

        //additional js:
        $variables['javascripts'] = array();
        $variables['js_variables'] = array();
        if(isset($options['js'])){
            foreach($options['js'] as $js){
                $variables['javascripts'][] = array('src' => $js);
            }
        }
        if(isset($options['js_var'])){
            foreach($options['js_var'] as $name => $value){
                $variables['js_variables'][$name] = $value;
            }
        }

        //user scripts
        $variables['user_scripts'] = $this->getUserScripts();

        $dataForDelivery = $this->getDataForDelivery();
        $variables['itemData'] = $dataForDelivery['core'];

        //copy all variable data into filtered array
        foreach($dataForDelivery['variable'] as $serial => $data){
            $filtered[$serial] = $data;
        }

        $variables['contentVariableElements'] = isset($options['contentVariableElements']) && is_array($options['contentVariableElements']) ? $options['contentVariableElements'] : array();
        $variables['tao_lib_path'] = isset($options['path']) && isset($options['path']['tao']) ? $options['path']['tao'] : '';
        $variables['taoQtiItem_lib_path'] = isset($options['path']) && isset($options['path']['taoQtiItem']) ? $options['path']['taoQtiItem'] : '';

        $tplRenderer = new taoItems_models_classes_TemplateRenderer($template, $variables);

        $returnValue = $tplRenderer->render();

        return (string) $returnValue;
    }
    
    protected function getUserScripts()
    {
        $userScripts = array();
        
        $userScriptConfig = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem')->getConfig('userScripts');
        if (is_array($userScriptConfig )) {
            foreach($userScriptConfig as $data){
                $userScripts[] = Template::js($data['src'], $data['extension']);
            }
        }
        return $userScripts;
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
            if($responseProcessing instanceof TemplatesDriven){
                $renderedResponseProcessing = $responseProcessing->buildQTI();
            }else{
                $renderedResponseProcessing = $responseProcessing->toQTI();
            }
        }

        // move item CSS class to itemBody
        $variables['class'] = $this->getAttributeValue('class');
        unset($variables['attributes']['class']);

        $variables['renderedResponseProcessing'] = $renderedResponseProcessing;

        return $variables;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return string
     * @throws exception\QtiModelException
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
                $parserValidator = new Parser($returnValue);
                $parserValidator->validate();
                if(!$parserValidator->isValid()){
                    common_Logger::w('Invalid QTI output: '.PHP_EOL.' '.$parserValidator->displayErrors());
                }
            }
        }else{
            $parserValidator = new Parser($qti);
            $parserValidator->validate();
            if(!$parserValidator->isValid()){
                throw new QtiModelException('Wrong QTI item output format');
            }
        }

        return (string) $returnValue;
    }
    
    /**
     * Serialize item object into json format, handy to be used in js
     */
    public function toArray($filterVariableContent = false, &$filtered = array()){
        $data = parent::toArray($filterVariableContent, $filtered);
        $data['namespaces'] = $this->getNamespaces();
        $data['stylesheets'] = $this->getArraySerializedElementCollection($this->getStylesheets(), $filterVariableContent, $filtered);
        $data['outcomes'] = $this->getArraySerializedElementCollection($this->getOutcomes(), $filterVariableContent, $filtered);
        $data['responses'] = $this->getArraySerializedElementCollection($this->getResponses(), $filterVariableContent, $filtered);
        $data['feedbacks'] = $this->getArraySerializedElementCollection($this->getModalFeedbacks(), $filterVariableContent, $filtered);
        $data['responseProcessing'] = $this->responseProcessing->toArray($filterVariableContent, $filtered);
        return $data;
    }

    public function getDataForDelivery(){

        $filtered = array();
        $itemData = $this->toArray(true, $filtered);
        unset($itemData['responseProcessing']);

        return array('core' => $itemData, 'variable' => $filtered);
    }

    /**
     * @return mixed
     */
    public function toForm(){

        $formContainer = new AssessmentItem($this);
        $returnValue = $formContainer->getForm();

        return $returnValue;
    }

}
