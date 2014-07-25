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
 * An outcome is a data build in item output. The SCORE is one of the most
 * outcomes.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10089
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_OutcomeDeclaration extends taoQTI_models_classes_QTI_VariableDeclaration
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'outcomeDeclaration';

    /**
     * The scale to used for this outcome, this is NOT supported in the QTI. It
     * be serialized in the session but excluded by extractVariables()
     *
     * @access protected
     * @var Scale
     */
    protected $scale = null;

    protected function getUsedAttributes(){
        return array_merge(
                parent::getUsedAttributes(), array(
            'taoQTI_models_classes_QTI_attribute_View',
            'taoQTI_models_classes_QTI_attribute_Interpretation',
            'taoQTI_models_classes_QTI_attribute_LongInterpretation',
            'taoQTI_models_classes_QTI_attribute_NormalMaximum',
            'taoQTI_models_classes_QTI_attribute_NormalMinimum',
            'taoQTI_models_classes_QTI_attribute_MasteryValue'
        ));
    }

    public function toArray(){
        $data = parent::toArray();
        $data['defaultValue'] = $this->getDefaultValue();
        $data['scale'] = $this->getScale();
        return $data;
    }

    protected function getTemplateQtiVariables(){
        $variables = parent::getTemplateQtiVariables();
        $variables['defaultValue'] = null;
        $defaultValue = $this->getDefaultValue();
        if(!is_null($defaultValue) || trim($defaultValue) != ''){
            $variables['defaultValue'] = $defaultValue;
        }
        return $variables;
    }

    /**
     * get the outcome in JSON format
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function toJSON(){
        $outcomeValue = null;
        if($this->defaultValue != ''){
            $outcomeValue = Array($this->defaultValue);
        }else if($this->getAttributeValue('baseType') == 'integer' || $this->getAttributeValue('baseType') == 'float'){
            $outcomeValue = Array(0);
        }else{
            $outcomeValue = null;
        }

        $returnValue = taoQTI_models_classes_Matching_VariableFactory::createJSONVariableFromQTIData(
                        $this->getIdentifier()
                        , $this->getAttributeValue('cardinality')
                        , $this->getAttributeValue('baseType')
                        , $outcomeValue
        );

        return $returnValue;
    }

    /**
     * used to extract the measurements of this item to the ontology
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item qtiItem
     * @return taoItems_models_classes_Measurement
     */
    public function toMeasurement(taoQTI_models_classes_QTI_Item $qtiItem){

        $interpretation = $this->getAttributeValue('interpretation');
        $returnValue = new taoItems_models_classes_Measurement($this->getIdentifier(), $interpretation);
        if(!is_null($this->getScale())){
            $returnValue->setScale($this->getScale());
        }
        $rp = $qtiItem->getResponseProcessing();
        if($rp instanceof taoQTI_models_classes_QTI_response_Composite){
            $irp = $rp->getIRPByOutcome($this);
            if(!is_null($irp)){
                $returnValue->setHumanAssisted($irp instanceof taoQTI_models_classes_QTI_response_interactionResponseProcessing_None);
            }
        }

        return $returnValue;
    }

    /**
     * Short description of method removeScale
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function removeScale(){
        $this->scale = null;
    }

    /**
     * Short description of method setScale
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Scale scale
     * @return mixed
     */
    public function setScale(taoItems_models_classes_Scale_Scale $scale){
        $this->scale = $scale;
    }

    /**
     * Short description of method getScale
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return taoItems_models_classes_Scale_Scale
     */
    public function getScale(){
        return $this->scale;
    }

}
/* end of class taoQTI_models_classes_QTI_OutcomeDeclaration */