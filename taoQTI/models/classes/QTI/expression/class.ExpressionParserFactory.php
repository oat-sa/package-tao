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
 * TAO -
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 23.01.2012, 17:10:01 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_expression
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A71-includes begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A71-includes end

/* user defined constants */
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A71-constants begin
// section 127-0-1-1-605722c1:12c112b6508:-8000:0000000000002A71-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoQTI
 * @subpackage models_classes_QTI_expression
 */
class taoQTI_models_classes_QTI_expression_ExpressionParserFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method build
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  SimpleXMLElement data
     * @return taoQTI_models_classes_QTI_expression_Expression
     */
    public static function build( SimpleXMLElement $data)
    {
        $returnValue = null;

        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002ADB begin
        $expression = null;
        $expressionName = $data->getName();
        
        //retrieve the expression attributes
        $attributes = array();
        foreach($data->attributes() as $key => $value){
            $attributes[$key] = (string)$value;
        }
        
        // Create expression function of its type (If specialization has been done for the expression type)
        $expressionClass = 'taoQTI_models_classes_QTI_expression_'.ucfirst($expressionName);
        
        if (class_exists($expressionClass)){
            $expression = new $expressionClass ($expressionName, $attributes);
        }
        else {
            $expression = new taoQTI_models_classes_QTI_expression_CommonExpression ($expressionName, $attributes);
        }
        
		// If the expression has a value
		$expressionValue = (string) trim($data);
		if ($expressionValue != ''){
			$expression->setValue($expressionValue);
		}
        
		// All sub-expressions of an expression are embedded by this expression
		$subExpressions = array();
		foreach ($data->children() as $subExpressionNode) {
			$subExpressions[] = self::build($subExpressionNode);
		}
		$expression->setSubExpressions($subExpressions);

        $returnValue = $expression;

        // section 127-0-1-1-2d3ac2b0:12c120718cc:-8000:0000000000002ADB end

        return $returnValue;
    }

} /* end of class taoQTI_models_classes_QTI_expression_ExpressionParserFactory */

?>