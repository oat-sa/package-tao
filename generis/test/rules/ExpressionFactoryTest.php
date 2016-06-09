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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

error_reporting(E_ALL);
use oat\generis\test\GenerisPhpUnitTestRunner;

/**
 *
 */
class ExpressionFactoryTestCase extends GenerisPhpUnitTestRunner {

    /**
     *
     */
    public function testCreateTerminalExpression(){
		$constantResource = core_kernel_rules_TermFactory::createConst('test1');
		$terminalExpression = core_kernel_rules_ExpressionFactory::createTerminalExpression($constantResource);
		$terminalExpressionProperty = new core_kernel_classes_Property(PROPERTY_TERMINAL_EXPRESSION,__METHOD__);
		$terminalExpressionVal = $terminalExpression->getOnePropertyValue($terminalExpressionProperty);
        $this->assertIsA($terminalExpressionVal,'core_kernel_classes_Resource');
        $this->assertEquals ($terminalExpressionVal->getUri(),$constantResource->getUri());

		$constantResource->delete();
		$terminalExpression->delete();
		
	}

    /**
     *
     */
    public function testCreateRecursiveExpression(){

        $constantResource1 = core_kernel_rules_TermFactory::createConst('test1');
        $constantResource2 = core_kernel_rules_TermFactory::createConst('test2');

        $terminalExpression1 = core_kernel_rules_ExpressionFactory::createTerminalExpression($constantResource1);
        $terminalExpression2 = core_kernel_rules_ExpressionFactory::createTerminalExpression($constantResource2);

        $equalsOperator = new core_kernel_classes_Resource(INSTANCE_EQUALS_OPERATOR_URI);
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression1,$terminalExpression2,$equalsOperator);

        //prop
        $terminalExpressionProperty = new core_kernel_classes_Property(PROPERTY_TERMINAL_EXPRESSION,__METHOD__);
        $logicalOperatorProperty = new core_kernel_classes_Property(PROPERTY_HASLOGICALOPERATOR,__METHOD__);
        $firstExpressionProperty = new core_kernel_classes_Property(PROPERTY_FIRST_EXPRESSION,__METHOD__);
        $secondExpressionProperty = new core_kernel_classes_Property(PROPERTY_SECOND_EXPRESSION,__METHOD__);

        //final expr
        $finalExpressionVal = $finalExpression->getOnePropertyValue($terminalExpressionProperty);
        $this->assertIsA($finalExpressionVal,'core_kernel_classes_Resource');
        $this->assertEquals ($finalExpressionVal->getUri(),INSTANCE_EMPTY_TERM_URI);

        //operator
        $logicalOperatorVal = $finalExpression->getOnePropertyValue($logicalOperatorProperty);
        $this->assertIsA($logicalOperatorVal,'core_kernel_classes_Resource');
        $this->assertEquals ($logicalOperatorVal->getUri(),INSTANCE_EQUALS_OPERATOR_URI);

        //first expr
        $firstExpressionVal = $finalExpression->getOnePropertyValue($firstExpressionProperty);
        $this->assertIsA($firstExpressionVal,'core_kernel_classes_Resource');
        $this->assertEquals ($firstExpressionVal->getUri(),$terminalExpression1->getUri());

        //Second expr
        $secondExpressionVal = $finalExpression->getOnePropertyValue($secondExpressionProperty);
        $this->assertIsA($secondExpressionVal,'core_kernel_classes_Resource');
        $this->assertEquals ($secondExpressionVal->getUri(),$terminalExpression2->getUri());

        $constantResource1->delete();
        $constantResource2->delete();
        $terminalExpression1->delete();
        $terminalExpression2->delete();
        $finalExpression->delete();

    }
	
}