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

namespace oat\taoQtiItem\model\qti;

use oat\taoQtiItem\model\qti\ResponseDeclaration;
use oat\taoQtiItem\model\qti\VariableDeclaration;
use oat\taoQtiItem\model\qti\response\Template;
use oat\taoQtiItem\model\qti\interaction\Interaction;
use oat\taoQtiItem\model\qti\response\SimpleFeedbackRule;
use \Exception;
use oat\taoQtiItem\model\qti\ContentVariable;

/**
 * A response is on object associated to an interactino containing which are the
 * response into the interaction choices and the score regarding the answers
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10073

 */
class ResponseDeclaration extends VariableDeclaration implements ContentVariable
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'responseDeclaration';

    /**
     * Short description of attribute correctResponses
     *
     * @access protected
     * @var array
     */
    protected $correctResponses = array();

    /**
     * Short description of attribute mapping
     *
     * @access protected
     * @var array
     */
    protected $mapping = array();

    /**
     * Short description of attribute areaMapping
     *
     * @access protected
     * @var array
     */
    protected $areaMapping = array();

    /**
     * Short description of attribute mappingDefaultValue
     *
     * @access protected
     * @var double
     */
    protected $mappingDefaultValue = 0.0;

    /**
     * Short description of attribute howMatch
     *
     * @access protected
     * @var String
     */
    protected $howMatch = null;
    protected $simpleFeedbackRules = array();

    protected function generateIdentifier($prefix = ''){
        
        if(empty($prefix)){
            $prefix = 'RESPONSE'; //QTI standard default value
        }

        return parent::generateIdentifier($prefix);
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){

        $data = parent::toArray($filterVariableContent, $filtered);
        //@todo : clean this please: do not use a class attributes to store childdren's ones.
        unset($data['attributes']['mapping']);
        unset($data['attributes']['areaMapping']);
        
        //prepare the protected data:
        $protectedData = array(
            'correctResponses' => $this->getCorrectResponses(),
            'mapping' => $this->mapping,
            'areaMapping' => $this->areaMapping,
            'howMatch' => $this->howMatch
        );
        
        //add mapping attributes
        $mappingAttributes = array('defaultValue' => $this->mappingDefaultValue);
        if(is_array($this->getAttributeValue('mapping'))){
            $mappingAttributes = array_merge($mappingAttributes, $this->getAttributeValue('mapping'));
        }elseif(is_array($this->getAttributeValue('areaMapping'))){
            $mappingAttributes = array_merge($mappingAttributes, $this->getAttributeValue('areaMapping'));
        }
        $protectedData['mappingAttributes'] = $mappingAttributes;
        
        //add simple feedbacks
        $feedbackRules = array();
        $rules = $this->getFeedbackRules();
        foreach($rules as $rule){
            $feedbackRules[$rule->getSerial()] = $rule->toArray($filterVariableContent, $filtered);
        }
        $protectedData['feedbackRules'] = $feedbackRules;
        
        if($filterVariableContent){
            $filtered[$this->getSerial()] = $protectedData;
        }else{
            $data = array_merge($data, $protectedData);
        }

        return $data;
    }

    protected function getTemplateQtiVariables(){

        $variables = parent::getTemplateQtiVariables();
        $variables['correctResponses'] = $this->getCorrectResponses();

        $variables['mapping'] = $this->mapping;
        $variables['areaMapping'] = $this->areaMapping;

        //@todo : clean this please: do not use a class attributes to store childdren's ones!
        unset($variables['attributes']['mapping']);
        unset($variables['attributes']['areaMapping']);

        $mappingAttributes = array('defaultValue' => $this->mappingDefaultValue);
        if(is_array($this->getAttributeValue('mapping'))){
            $mappingAttributes = array_merge($mappingAttributes, $this->getAttributeValue('mapping'));
        }elseif(is_array($this->getAttributeValue('areaMapping'))){
            $mappingAttributes = array_merge($mappingAttributes, $this->getAttributeValue('areaMapping'));
        }

        $variables['mappingAttributes'] = $this->xmlizeOptions($mappingAttributes, true);

        $rpTemplate = '';
        switch($this->howMatch){
            case Template::MATCH_CORRECT:{
                    $rpTemplate = 'match_correct';
                    break;
                }
            case Template::MAP_RESPONSE:{
                    $rpTemplate = 'map_response';
                    break;
                }
            case Template::MAP_RESPONSE_POINT:{
                    $rpTemplate = 'map_response_point';
                    break;
                }
        }
        $variables['howMatch'] = $this->howMatch; //the template
        $variables['rpTemplate'] = $rpTemplate; //the template

        return $variables;
    }

    /**
     * Short description of method getCorrectResponses
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getCorrectResponses(){
        return (array) $this->correctResponses;
    }

    /**
     * Short description of method setCorrectResponses
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array responses
     * @return mixed
     */
    public function setCorrectResponses($responses){
        if(!is_array($responses)){
            $responses = array($responses);
        }
        $this->correctResponses = $responses;
    }

    /**
     * Short description of method getMapping
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string type
     * @return array
     */
    public function getMapping($type = ''){
        $returnValue = array();

        if($type == 'area'){
            $returnValue = $this->areaMapping;
        }else{
            $returnValue = $this->mapping;
        }

        return (array) $returnValue;
    }

    /**
     * Short description of method setMapping
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array map
     * @param  type
     * @return mixed
     */
    public function setMapping($map, $type = ''){
        if($type == 'area'){
            $this->areaMapping = $map;
        }else{
            $this->mapping = $map;
        }
    }

    public function setMappingAttributes($attributes){
        $this->setAttribute('mapping', $attributes);
    }

    /**
     * Short description of method getMappingDefaultValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return double
     */
    public function getMappingDefaultValue(){
        return $this->mappingDefaultValue;
    }

    /**
     * Short description of method setMappingDefaultValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  double value
     * @return mixed
     */
    public function setMappingDefaultValue($value){
        $this->mappingDefaultValue = floatval($value);
    }

    /**
     * get the correct response in JSON format. If no correct response defined
     * null.
     * 
     * @deprecated now use the new qtism lib for response evaluation
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function correctToJSON(){
        $returnValue = null;

        try{
            $correctResponses = $this->getCorrectResponses();
            if(count($correctResponses)){
                $returnValue = taoQTI_models_classes_Matching_VariableFactory::createJSONVariableFromQTIData(
                                $this->getIdentifier()
                                , $this->getAttributeValue('cardinality')
                                , $this->getAttributeValue('baseType')
                                , $this->correctResponses
                );
            }
        }catch(Exception $e){
            
        }

        return $returnValue;
    }

    /**
     * Short description of method areaMapToJson
     *
     * @deprecated now use the new qtism lib for response evaluation
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function areaMapToJson(){
        $returnValue = null;

        // Get the stored area mapping
        $mapping = $this->getMapping('area');

        if(count($mapping)){
            $returnValue = Array();
            $returnValue['identifier'] = $this->getIdentifier();
            $returnValue['defaultValue'] = $this->mappingDefaultValue;
            if($this->hasAttribute('areaMapping')){
                $returnValue = array_merge($returnValue, $this->getAttributeValue('areaMapping'));
            }
            $mappingValue = Array();

            // If a mapping has been defined
            if(!empty($mapping)){
                foreach($mapping as $mapKey => $mappedValue){
                    $areaMapEntryJSON = Array();
                    $areaMapEntryJSON['value'] = (float) $mappedValue["mappedValue"];
                    $areaMapEntryJSON['key'] = taoQTI_models_classes_Matching_VariableFactory::createJSONShapeFromQTIData($mappedValue);
                    array_push($mappingValue, (object) $areaMapEntryJSON);
                }
                $returnValue['value'] = $mappingValue;
            }

            $returnValue = (object) $returnValue;
        }

        return $returnValue;
    }

    /**
     * get the mapping in JSON format. If no mapping defined return null.
     *  
     * @deprecated now use the new qtism lib for response evaluation
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function mapToJSON(){
        $returnValue = null;

        $mapping = $this->getMapping();
        if(count($mapping)){
            $returnValue = Array();
            $returnValue['identifier'] = $this->getIdentifier();
            $returnValue['defaultValue'] = $this->mappingDefaultValue;
            if($this->hasAttribute('areaMapping')){
                $returnValue = array_merge($returnValue, $this->getAttributeValue('areaMapping'));
            }
            $mappingValue = Array();

            // If a mapping has been defined
            if(!empty($mapping)){
                foreach($mapping as $mapKey => $mappedValue){
                    $mapEntryJSON = Array();
                    $mapEntryJSON['value'] = (float) $mappedValue;
                    $mapEntryJSON['key'] = taoQTI_models_classes_Matching_VariableFactory::createJSONValueFromQTIData($mapKey, $this->getAttributeValue('baseType'));
                    array_push($mappingValue, (object) $mapEntryJSON);
                }

                $returnValue['value'] = $mappingValue;
            }

            $returnValue = (object) $returnValue;
        }

        return $returnValue;
    }

    /**
     * get the base type of the response declaration
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getBaseType(){
        return (string) $this->getAttributeValue('baseType');
    }

    /**
     * Short description of method getHowMatch
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getHowMatch(){
        return (string) $this->howMatch;
    }

    /**
     * Short description of method setHowMatch
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string howMatch
     * @return mixed
     */
    public function setHowMatch($howMatch){
        $this->howMatch = $howMatch;
    }

    public function getAssociatedInteraction(){
        $returnValue = null;
        $item = $this->getRelatedItem();
        if(!is_null($item)){
            $interactions = $item->getInteractions();
            foreach($interactions as $interaction){
                if($interaction->getAttributeValue('responseIdentifier') == $this->getIdentifier()){
                    $returnValue = $interaction;
                    break;
                }
            }
        }
        return $returnValue;
    }

    /**
     * Short description of method toForm
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return tao_helpers_form_xhtml_Form
     */
    public function toForm(){
        $returnValue = null;

        $interaction = $this->getAssociatedInteraction();
        if($interaction instanceof Interaction){
            $responseFormClass = '\\oat\\taoQtiItem\\controller\\QTIform\\response\\'.ucfirst(strtolower($interaction->getType())).'Interaction';
            if(class_exists($responseFormClass)){
                $formContainer = new $responseFormClass($this);
                $myForm = $formContainer->getForm();
                $returnValue = $myForm;
            }
        }else{
            throw new Exception('cannot find the parent interaction of the current response');
        }

        return $returnValue;
    }

    public function addFeedbackRule(SimpleFeedbackRule $simpleFeedbackRule){
        $this->simpleFeedbackRules[$simpleFeedbackRule->getSerial()] = $simpleFeedbackRule;
        $simpleFeedbackRule->setRelatedItem($this->getRelatedItem());
    }

    public function getFeedbackRules(){
        return $this->simpleFeedbackRules;
    }

    public function getFeedbackRule($serial){
        return isset($this->simpleFeedbackRules[$serial]) ? $this->simpleFeedbackRules[$serial] : null;
    }

    public function removeFeedbackRule($serial){
        unset($this->simpleFeedbackRules[$serial]);
        return true;
    }

    public function toFilteredArray(){
        return $this->toArray(true);
    }

}