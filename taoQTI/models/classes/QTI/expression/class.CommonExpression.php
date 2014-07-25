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
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoQTI/models/classes/QTI/expression/class.CommonExpression.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 16.01.2012, 18:09:12 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_expression
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoQTI_models_classes_QTI_expression_Expression
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoQTI/models/classes/QTI/expression/class.Expression.php');

/**
 * include taoQTI_models_classes_QTI_response_Rule
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoQTI/models/classes/QTI/response/interface.Rule.php');

/* user defined includes */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A70-includes begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A70-includes end

/* user defined constants */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A70-constants begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A70-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_expression
 */
class taoQTI_models_classes_QTI_expression_CommonExpression
    extends taoQTI_models_classes_QTI_expression_Expression
        implements taoQTI_models_classes_QTI_response_Rule
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

        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF begin
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
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF end

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
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000004900 begin
        $this->name = $name;
        $this->attributes = $attributes;
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000004900 end
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
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AC7 begin
        $this->subExpressions = $expressions;
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AC7 end
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
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AD8 begin
     
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
        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002AD8 end
    }

} /* end of class taoQTI_models_classes_QTI_expression_CommonExpression */

?>