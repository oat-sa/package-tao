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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\rules\class.ExpressionFactory.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 23.03.2010, 15:58:22 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_rules
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001742-includes begin
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001742-includes end

/* user defined constants */
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001742-constants begin
// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001742-constants end

/**
 * Short description of class core_kernel_rules_ExpressionFactory
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_rules
 */
class core_kernel_rules_ExpressionFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method createTerminalExpression
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Term term
     * @return core_kernel_rules_Expression
     */
    public static function createTerminalExpression( core_kernel_rules_Term $term)
    {
        $returnValue = null;

        // section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001747 begin
        if ($term == null ) {
        	var_dump($term);
        	throw new common_Exception('paramaters could not be null');
        }
        $expressionClass = new core_kernel_classes_Class(CLASS_EXPRESSION,__METHOD__);
        $label = 'Terminal Expression : ' . $term->getLabel();
        $comment = 'Terminal Expression : ' . $term->getUri();
        $expressionInst = core_kernel_classes_ResourceFactory::create($expressionClass,$label,$comment);
      	$terminalExpressionProperty = new core_kernel_classes_Property(PROPERTY_TERMINAL_EXPRESSION,__METHOD__);
        $returnValue = new core_kernel_rules_Expression($expressionInst->getUri());
        $returnValue->setPropertyValue($terminalExpressionProperty,$term->getUri());
        $returnValue->debug = __METHOD__;
        // section 10-13-1--99-2335bfbb:1207fc834f5:-8000:0000000000001747 end

        return $returnValue;
    }

    /**
     * Short description of method createRecursiveExpression
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Expression exp1
     * @param  Expression exp2
     * @param  Resource operator
     * @return core_kernel_rules_Expression
     */
    public static function createRecursiveExpression( core_kernel_rules_Expression $exp1,  core_kernel_rules_Expression $exp2,  core_kernel_classes_Resource $operator)
    {
        $returnValue = null;

        // section 10-13-1--99-2335bfbb:1207fc834f5:-8000:000000000000174A begin
        if ($exp1 == null || $exp2 == null  || $operator == null) {
        	var_dump($exp1,$exp2,$operator);
        	throw new common_Exception('paramaters could not be null');
        }

        $expressionClass = new core_kernel_classes_Class(CLASS_EXPRESSION,__METHOD__);
        $label = 'Expression : ' . $exp1->getLabel() . ' ' . $operator->getLabel() . ' ' . $exp2->getLabel();
        $comment = 'Expression : ' . $exp1->getUri() . ' ' . $operator->getUri() . ' ' . $exp2->getUri();
        $expressionInst = core_kernel_classes_ResourceFactory::create($expressionClass,$label,$comment);
      	$terminalExpressionProperty = new core_kernel_classes_Property(PROPERTY_TERMINAL_EXPRESSION,__METHOD__);
        $logicalOperatorProperty = new core_kernel_classes_Property(PROPERTY_HASLOGICALOPERATOR,__METHOD__);
		$firstExpressionProperty = new core_kernel_classes_Property(PROPERTY_FIRST_EXPRESSION,__METHOD__);
		$secondExpressionProperty = new core_kernel_classes_Property(PROPERTY_SECOND_EXPRESSION,__METHOD__);
		$returnValue = new core_kernel_rules_Expression($expressionInst->getUri());
		$returnValue->debug = __METHOD__;
		$returnValue->setPropertyValue($terminalExpressionProperty,INSTANCE_EMPTY_TERM_URI);
		$returnValue->setPropertyValue($firstExpressionProperty,$exp1->getUri());
		$returnValue->setPropertyValue($secondExpressionProperty,$exp2->getUri());
		$returnValue->setPropertyValue($logicalOperatorProperty,$operator->getUri()); 
		// section 10-13-1--99-2335bfbb:1207fc834f5:-8000:000000000000174A end

        return $returnValue;
    }

} /* end of class core_kernel_rules_ExpressionFactory */

?>