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

use oat\taoQtiItem\model\qti\OutcomeDeclaration;
use oat\taoQtiItem\model\qti\VariableDeclaration;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\response\Composite;
use oat\taoQtiItem\model\qti\response\interactionResponseProcessing\None;

/**
 * An outcome is a data build in item output. The SCORE is one of the most
 * outcomes.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10089
 
 */
class OutcomeDeclaration extends VariableDeclaration
{

    /**
     * the QTI tag name as defined in QTI standard
     *
     * @access protected
     * @var string
     */
    protected static $qtiTagName = 'outcomeDeclaration';

    protected function getUsedAttributes(){
        return array_merge(
                parent::getUsedAttributes(), array(
            'oat\\taoQtiItem\\model\\qti\\attribute\\View',
            'oat\\taoQtiItem\\model\\qti\\attribute\\Interpretation',
            'oat\\taoQtiItem\\model\\qti\\attribute\\LongInterpretation',
            'oat\\taoQtiItem\\model\\qti\\attribute\\NormalMaximum',
            'oat\\taoQtiItem\\model\\qti\\attribute\\NormalMinimum',
            'oat\\taoQtiItem\\model\\qti\\attribute\\MasteryValue'
        ));
    }

    public function toArray($filterVariableContent = false, &$filtered = array()){
        $data = parent::toArray($filterVariableContent, $filtered);
        $data['defaultValue'] = $this->getDefaultValue();
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
     * @deprecated now use the new qtism lib for response evaluation
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
}
