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
namespace oat\taoQtiItem\model\qti\expression;

use oat\taoQtiItem\model\qti\expression\CommonExpression;
use oat\taoQtiItem\model\qti\expression\Expression;
use oat\taoQtiItem\model\qti\response\Rule;
use \common_Exception;
use \Exception;

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 
 */
class CommonExpression
    extends Expression
        implements Rule
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute subExpressions
     *
     * @access protected
     * @var array
     */
    protected $subExpressions = array();

    /**
     * Short description of attribute value
     *
     * @access protected
     */
    protected $value = null;

    /**
     * Short description of attribute name
     *
     * @access protected
     * @var string
     */
    protected $name = '';

    /**
     * Short description of attribute attributes
     *
     * @access protected
     * @var array
     */
    protected $attributes = array();

    // --- OPERATIONS ---

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRule()
    {
        $returnValue = (string) '';

        
    // Get subExpressions
        $subExpressionsRules = array();
        foreach ($this->subExpressions as $subExpression){
            $subExpressionsRules[] = $subExpression->getRule();
        }
        $subExpressionsJSON = implode(',', $subExpressionsRules);

        // Format options
        $optionsJSON = count($this->attributes) ? '"'.addslashes(json_encode($this->attributes)).'"' : 'null';
        
        // Format rule function of the expression operator
        switch ($this->name) {
            case 'correct':
                $returnValue = 'getCorrect("'.$this->attributes['identifier'].'")';
                break;
            case 'mapResponse':
                $identifier = $this->attributes['identifier'];
                $returnValue = 'mapResponse('
                    . $optionsJSON
                    .', getMap("'.$identifier.'"), getResponse("'.$identifier.'"))';
                break;
            // Multiple is a Creation of List from parameters
            case 'multiple':
                $returnValue = 'createVariable("{\"type\":\"list\"}", '.$subExpressionsJSON.')';
                break;
            // Null is a Creation of empty BaseTypeVariable
            case 'null':
                $returnValue = 'createVariable(null, null)';
                break;
            // Ordered is a Creation of Tuple from parameters
            case 'ordered':
                $returnValue = 'createVariable("{\"type\":\"tuple\"}", '.$subExpressionsJSON.')';
                break;
            case 'outcome':
                $returnValue = 'getOutcome("'.$this->attributes['identifier'].'")';
                break;
            case 'setOutcomeValue':
                //@todo remove this
                throw new common_Exception('setOutcomeValue is not a valid expression');
                break;
            case 'variable':
                $returnValue = 'getVariable("'.$this->attributes['identifier'].'")';
                break;
            
            default:                 
                $returnValue = 
                    $this->name.'('
                        . $optionsJSON
                        . ($subExpressionsJSON!="" ? ', '.$subExpressionsJSON : '')
                    .')';
        }
        

        return (string) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string name
     * @param  array attributes
     * @return mixed
     */
    public function __construct($name, $attributes)
    {
        
        $this->name = $name;
        $this->attributes = $attributes;
        
    }

    /**
     * Short description of method setSubExpressions
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array expressions
     * @return mixed
     */
    public function setSubExpressions($expressions)
    {
        
        $this->subExpressions = $expressions;
        
    }

    /**
     * Short description of method setValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  value
     * @return mixed
     */
    public function setValue(   $value)
    {
        
     
        // Set the value of the expression and cast it function of the (defined) base type of the variable
        if ($this->attributes['baseType']){
            switch ($this->attributes['baseType']){
                case 'boolean':
                    if (is_string ($value)){
                        $this->value = (bool)($value=='true'||$value=='1'?1:0);
                    }else if (is_bool ($value)){
                        $this->value = $value;
                    }else if ($value == null){
                        $this->value = null;
                    }else{
                        throw new Exception ('taoQTI_models_classes_QTI_response_ExpressionOperator::setValue : an error occured, the value ['.$value.'] is not a well formed boolean');
                    }
                    break;
                case 'float':
                    $this->value = (float)$value;
                    break;
                case 'integer':
                    $this->value = (int)$value;
                    break;
                case 'identifier':
                case 'string':
                    $this->value = (string)$value;
                    break;
                case 'pair':
                    $this->value = taoQTI_models_classes_Matching_VariableFactory::createJSONValueFromQTIData($value, 'pair');
                    break;
                case 'directedPair':
                    $this->value = taoQTI_models_classes_Matching_VariableFactory::createJSONValueFromQTIData($value, 'directedPair');
                    break;
            }   
        }
        
    }

}

?>