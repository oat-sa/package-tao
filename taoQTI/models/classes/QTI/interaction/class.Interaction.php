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
 * The main QTI Interaction Class.
 * Although a QTI Interaction has not the identifier attribute,
 * it is defined as an IdentifiedElement internally to enable multiple-interaction items
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10247
 * @subpackage models_classes_QTI
 */
abstract class taoQTI_models_classes_QTI_interaction_Interaction extends taoQTI_models_classes_QTI_IdentifiedElement implements taoQTI_models_classes_QTI_IdentifiedElementContainer
{

    /**
     * Define the class of choice associate to the interaction, to be overwritten
     * by concrete class
     *
     * @var choiceClass
     */
    protected static $choiceClass = '';
    static protected $baseType = '';

    /**
     * The response of the interaction
     *
     * @access protected
     * @var Response
     */
    protected $choices = array();

    protected function getUsedAttributes(){
        return array(
            'taoQTI_models_classes_QTI_attribute_ResponseIdentifier'
        );
    }

    public function getChoices($setNumber = null){
        return $this->choices;
    }

    public function getChoiceBySerial($serial){
        $returnValue = null;
        $choices = $this->getChoices();
        if(isset($choices[$serial])){
            $returnValue = $choices[$serial];
        }
        return $returnValue;
    }

    public function getChoiceByIdentifier($identifier){
        return $this->getIdentifiedElements()->getUnique($identifier, 'taoQTI_models_classes_QTI_choice_Choice');
    }

    public function addChoice(taoQTI_models_classes_QTI_choice_Choice $choice, $setNumber = null){
        $returnValue = false;

        if(!empty(static::$choiceClass) && get_class($choice) == static::$choiceClass){
            $this->choices[$choice->getSerial()] = $choice;
            $relatedItem = $this->getRelatedItem();
            if(!is_null($relatedItem)){
                $choice->setRelatedItem($relatedItem);
            }
            $returnValue = true;
        }else{
            throw new InvalidArgumentException('Wrong type of choice in argument: '.static::$choiceClass);
        }

        return $returnValue;
    }

    /**
     *
     * @return taoQTI_models_classes_QTI_choice_Choice
     */
    public function createChoice($choiceAttributes = array(), $choiceValue = null, $setNumber = null){
        $returnValue = null;

        if(!empty(static::$choiceClass) && is_subclass_of(static::$choiceClass, 'taoQTI_models_classes_QTI_choice_Choice')){
            $returnValue = new static::$choiceClass($choiceAttributes);
            $returnValue->setContent($choiceValue);
            $this->addChoice($returnValue);
        }

        return $returnValue;
    }

    public function removeChoice(taoQTI_models_classes_QTI_choice_Choice $choice, $setNumber = null){
        unset($this->choices[$choice->getSerial()]);
    }

    public function getIdentifiedElements(){
        $returnValue = new taoQTI_models_classes_QTI_IdentifierCollection();
        $returnValue->addMultiple($this->getChoices());

        return $returnValue;
    }

    protected function getTemplateQtiVariables(){
        $variables = parent::getTemplateQtiVariables();
        // remove the identifier attribute to comply with the standard, it is used interally to manage multiple interactions within a single item.
        unset($variables['attributes']['identifier']);
        $variables['choices'] = '';
        foreach($this->getChoices() as $choice){
            $variables['choices'] .= $choice->toQTI();
        }
        return $variables;
    }

    /**
     * Get the response declaration associated to the interaction
     * If no response exists, one will be created
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return taoQTI_models_classes_QTI_ResponseDeclaration
     */
    public function getResponse(){
        $returnValue = null;

        $responseAttribute = $this->getAttribute('responseIdentifier');
        if(!is_null($responseAttribute)){
            $idenfierBaseType = $responseAttribute->getValue(true);
            if(!is_null($idenfierBaseType)){
                $returnValue = $idenfierBaseType->getReferencedObject();
            }else{
                $responseDeclaration = new taoQTI_models_classes_QTI_ResponseDeclaration();
                if($this->setResponse($responseDeclaration)){
                    $returnValue = $responseDeclaration;
                }else{
                    throw new taoQTI_models_classes_QTI_QtiModelException('cannot create the interaction response');
                }
            }
        }

        return $returnValue;
    }

    /**
     * Define the interaction's response
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param
     *            taoQTI_models_classes_QTI_ResponseDeclaration response
     * @return mixed
     */
    public function setResponse(taoQTI_models_classes_QTI_ResponseDeclaration $response){
        $relatedItem = $this->getRelatedItem();
        if(!is_null($relatedItem)){
            $relatedItem->addResponse($response);
        }
        return $this->setAttribute('responseIdentifier', $response);
    }

    /**
     * Retrieve the interaction cardinality
     * (single, multiple or ordered)
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param
     *            boolean numeric
     * @return mixed
     */
    public function getCardinality($numeric = false){
        $returnValue = null;

        // get maximum possibility:
        switch(strtolower($this->getType())){
            case 'choice':
            case 'hottext':
            case 'hotspot':
            case 'selectpoint':
            case 'positionobject':{
                    $max = intval($this->getAttributeValue('maxChoices'));
                    if($numeric){
                        $returnValue = $max;
                    }
                    else {
                        $returnValue = ($max == 1) ? 'single' : 'multiple'; // default=1
                    }
                    break;
                }
            case 'associate':
            case 'match':
            case 'graphicassociate':{
                    $max = intval($this->getAttributeValue('maxAssociations'));
                    if($numeric){
                        $returnValue = $max;
                    }
                    else{
                        $returnValue = ($max == 1) ? 'single' : 'multiple';
                    } // default=1
                    break;
                }
            case 'extendedtext':{
                    // maxStrings + order or not?
                    $cardinality = $this->getAttributeValue('cardinality');
                    if($cardinality == 'ordered'){
                        if($numeric){
                            $returnValue = 0;
                        } // meaning, infinite
                        else {
                            $returnValue = $cardinality;
                        }
                        break;
                    }
                    $max = intval($this->getAttributeValue('maxStrings'));
                    if($numeric){
                        $returnValue = $max;
                    }
                    else {
                        $returnValue = ($max > 1) ? 'multiple' : 'single'; // optional
                    }
                    break;
                }
            case 'gapmatch':{
                    // count the number of gap, i.e. "groups" in the interaction:
                    $max = count($this->getGaps());
                    if($numeric) {
                        $returnValue = $max;
                    }
                    else {
                        $returnValue = ($max > 1) ? 'multiple' : 'single';
                    }
                    break;
                }
            case 'graphicgapmatch':{
                    // strange that the standard always specifies "multiple":
                    $returnValue = ($numeric) ? 0 : 'multiple';
                    break;
                }
            case 'order':
            case 'graphicorder':{
                    $returnValue = ($numeric) ? 1 : 'ordered';
                    break;
                }
            case 'inlinechoice':
            case 'textentry':
            case 'media':
            case 'slider':
            case 'upload':
            case 'endattempt':{
                    $returnValue = ($numeric) ? 1 : 'single';
                    break;
                }
            default:{
                    throw new taoQTI_models_classes_QTI_QtiModelException("the current interaction type \"{$this->type}\" is not available yet");
                }
        }

        return $returnValue;
    }

    /**
     * Get the interaction base type:
     * integer, string, identifier, pair, directedPair
     * float, boolean or point
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return string
     */
    public function getBaseType(){
        return strtolower(static::$baseType);
    }

    public static function getTemplateHtml(){
        if(empty(static::$qtiTagName)){
            throw new taoQTI_models_classes_QTI_QtiModelException('The element has no tag name defined : '.get_called_class());
        }
        $interactionName = strtolower(str_replace('Interaction', '', static::$qtiTagName));
        $template = static::getTemplatePath().'/xhtml.'.$interactionName.'.tpl.php';
        if(!file_exists($template)){
            $template = static::getTemplatePath().'/xhtml.interaction.tpl.php';
        }

        return $template;
    }

    protected function getTemplateHtmlVariables(){
        $variables = array();
        $attributes = $this->getAttributeValues();
        $variables['class'] = '';
        if(isset($attributes['class'])){
            $variables['class'] = $attributes['class'];
            unset($attributes['class']);
        }

        $variables['qti_initParam'] = $attributes;
        $variables['qti_initParam']['id'] = $this->getIdentifier(true);

        // change from camelCase to underscore_case the type of the interaction to be used in the JS
        $type = get_class($this);
        $type = substr($type, strrpos($type, '_'));
        $type = str_replace('Interaction', '', $type);
        $type = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $type));
        $variables['_type'] = $type;
        $variables['qti_initParam']['type'] = "qti_<?= $type ?>_interaction";

        if($this instanceof taoQTI_models_classes_QTI_interaction_ObjectInteraction){
            $objectAttributes = $this->getObject()->getAttributeValues();
            $variables['qti_initParam']['imagePath'] = $objectAttributes['data'];
            if(!empty($objectAttributes['width'])){
                $variables['qti_initParam']['imageWidth'] = $objectAttributes['width'];
            }
            if(!empty($objectAttributes['height'])){
                $variables['qti_initParam']['imageHeight'] = $objectAttributes['height'];
            }
        }

        // Give to the template the response base type linked to this interaction
        // @todo check if this information is not yet available
        $response = $this->getResponse();
        if($response != null){
            $variables['qti_initParam']['responseBaseType'] = $response->getBaseType();
        }

        if($this instanceof taoQTI_models_classes_QTI_interaction_BlockInteraction){
            $variables['prompt'] = $this->prompt->toXHTML();
        }

        switch(get_class($this)){
            case 'taoQTI_models_classes_QTI_interaction_ChoiceInteraction':
            case 'taoQTI_models_classes_QTI_interaction_AssociateInteraction':
            case 'taoQTI_models_classes_QTI_interaction_OrderInteraction':
            case 'taoQTI_models_classes_QTI_interaction_GapMatchInteraction':
                $variables['choices'] = '<ul class="qti_choice_list">';
                foreach($this->getChoices() as $choice){
                    $variables['choices'] .= $choice->toXHTML();
                }
                $variables['choices'] .= '</ul>';
                break;
            case 'taoQTI_models_classes_QTI_interaction_MatchInteraction':
                $variables['choices'] = '';
                for($i = 0; $i < 1; $i++){
                    $variables['choices'] .= '<ul class="qti_choice_list">';
                    foreach($this->getChoices($i) as $choice){
                        $variables['choices'] .= $choice->toXHTML();
                    }
                    $variables['choices'] .= '</ul>';
                }
                break;
        }

        if($this instanceof taoQTI_models_classes_QTI_interaction_GraphicInteraction){
            $variables['choices'] = '<ul class="qti_'.$variables['_type'].'_spotlist">';
            foreach($this->getChoices() as $choice){
                $variables['choices'] .= $choice->toXHTML();
            }
            $variables['choices'] .= '</ul>';
        }

        if($this instanceof taoQTI_models_classes_QTI_interaction_ContainerInteraction){
            $variables['body'] = $this->getBody()->toXHTML();
        }

        return $variables;
    }

    /**
     * Legacy function to pre-render the interactions toXHTML
     * To be replace by a clean client side OO implementation
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return string
     */
    public function toXHTML(){
        $template = static::getTemplateHtml();
        $variables = $this->getTemplateHtmlVariables();

        $tplRenderer = new taoItems_models_classes_TemplateRenderer($template, $variables);
        $returnValue = $tplRenderer->render();

        return (string) $returnValue;
    }

    public function toArray(){
        $data = parent::toArray();
        $data['choices'] = array();
        foreach($this->getChoices() as $choice){
            $data['choices'][$choice->getSerial()] = $choice->toArray();
        }
        return $data;
    }

    /**
     * Short description of method canRenderTesttakerResponse
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return boolean
     */
    public function canRenderTesttakerResponse(){
        $returnValue = in_array(strtolower($this->type), array(
            'extendedtext'
        ));

        return (bool) $returnValue;
    }

    /**
     * Short description of method renderTesttakerResponseXHTML
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param
     *            responses
     * @return string
     */
    public function renderTesttakerResponseXHTML($responses){
        throw new taoQTI_models_classes_QTI_QtiModelException('method to be reimplemented');
    }

    public function getType(){
        $tagName = static::$qtiTagName;
        return str_replace('Interaction', '', $tagName);
    }

    public function toForm(){
        $returnValue = null;

        $interactionFormClass = 'taoQTI_actions_QTIform_interaction_'.ucfirst(strtolower($this->getType())).'Interaction';
        if(!class_exists($interactionFormClass)){
            throw new Exception("the class {$interactionFormClass} does not exist");
        }else{
            $formContainer = new $interactionFormClass($this);
            $myForm = $formContainer->getForm();
            $returnValue = $myForm;
        }

        return $returnValue;
    }

}