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

use oat\generis\test\GenerisPhpUnitTestRunner;

class ExpressionTest extends GenerisPhpUnitTestRunner {

    public function testEvaluate(){
        $constantResource1 = core_kernel_rules_TermFactory::createConst('test1');
        $constantResource2 = core_kernel_rules_TermFactory::createConst('test2');

        $constantResource14 = core_kernel_rules_TermFactory::createConst('14');
        $constantResource12 = core_kernel_rules_TermFactory::createConst('12');
        $constantResource12b = core_kernel_rules_TermFactory::createConst('12');

        $true = new core_kernel_rules_Expression(INSTANCE_EXPRESSION_TRUE);
        $this->assertTrue($true->evaluate());

        $false = new core_kernel_rules_Expression(INSTANCE_EXPRESSION_FALSE);
        $this->assertFalse($false->evaluate());

        $terminalExpression1 = core_kernel_rules_ExpressionFactory::createTerminalExpression($constantResource1);
        $terminalExpression2 = core_kernel_rules_ExpressionFactory::createTerminalExpression($constantResource2);

        $terminalExpression14 = core_kernel_rules_ExpressionFactory::createTerminalExpression($constantResource14);
        $terminalExpression12 = core_kernel_rules_ExpressionFactory::createTerminalExpression($constantResource12);
        $terminalExpression12b = core_kernel_rules_ExpressionFactory::createTerminalExpression($constantResource12b);


        // test1 == test2
        $equalsOperator = new core_kernel_classes_Resource(INSTANCE_EQUALS_OPERATOR_URI);
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression1,$terminalExpression2,$equalsOperator);
        $this->assertFalse($finalExpression->evaluate());
        $finalExpression->delete();

        // 12 == 12
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression12,$terminalExpression12b,$equalsOperator);
        $this->assertTrue($finalExpression->evaluate());
        $finalExpression->delete();

        // test1 != test2
        $diffOperator = new core_kernel_classes_Resource(INSTANCE_DIFFERENT_OPERATOR_URI);
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression1,$terminalExpression2,$diffOperator);
        $this->assertTrue($finalExpression->evaluate());
        $finalExpression->delete();

        // 12 != 12
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression12,$terminalExpression12b,$diffOperator);
        $this->assertFalse($finalExpression->evaluate());
        $finalExpression->delete();

        // 14 <= 12
        $infEqOperator = new core_kernel_classes_Resource(INSTANCE_INF_EQ_OPERATOR_URI);
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression14,$terminalExpression12,$infEqOperator);
        $this->assertFalse($finalExpression->evaluate());
        $finalExpression->delete();

        //12 <= 14
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression12,$terminalExpression14,$infEqOperator);
        $this->assertTrue($finalExpression->evaluate());
        $finalExpression->delete();

        //12 <= 12
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression12,$terminalExpression12b,$infEqOperator);
        $this->assertTrue($finalExpression->evaluate());
        $finalExpression->delete();

        // 14 >= 12
        $supEqOperator = new core_kernel_classes_Resource(INSTANCE_SUP_EQ_OPERATOR_URI);
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression14,$terminalExpression12,$supEqOperator);
        $this->assertTrue($finalExpression->evaluate());
        $finalExpression->delete();

        //12 >= 14
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression12,$terminalExpression14,$supEqOperator);
        $this->assertFalse($finalExpression->evaluate());
        $finalExpression->delete();

        //12 >= 12
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression12,$terminalExpression12b,$supEqOperator);
        $this->assertTrue($finalExpression->evaluate());
        $finalExpression->delete();


        // 14 < 12
        $infOperator = new core_kernel_classes_Resource(INSTANCE_INF_OPERATOR_URI);
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression14,$terminalExpression12,$infOperator);
        $this->assertFalse($finalExpression->evaluate());
        $finalExpression->delete();

        //12 < 14
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression12,$terminalExpression14,$infOperator);
        $this->assertTrue($finalExpression->evaluate());
        $finalExpression->delete();

        //12 < 12
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression12,$terminalExpression12b,$infOperator);
        $this->assertFalse($finalExpression->evaluate());
        $finalExpression->delete();

        // 14 > 12
        $supOperator = new core_kernel_classes_Resource(INSTANCE_SUP_OPERATOR_URI);
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression14,$terminalExpression12,$supOperator);
        $this->assertTrue($finalExpression->evaluate());
        $finalExpression->delete();

        //12 > 14
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression12,$terminalExpression14,$supOperator);
        $this->assertFalse($finalExpression->evaluate());
        $finalExpression->delete();

        //12 > 12
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression12,$terminalExpression12b,$supOperator);
        $this->assertFalse($finalExpression->evaluate());
        $finalExpression->delete();

        // trueExpression => 12 < 14
        $trueExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression12,$terminalExpression14,$infOperator);
        //falseExpression =>  test1 == test2
        $falseExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression1,$terminalExpression2,$equalsOperator);
        // trueExpression2 =>  test1 != test2
        $trueExpression2 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression1,$terminalExpression2,$diffOperator);
        //falseExpression2 =>  14 < 12
        $falseExpression2 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression14,$terminalExpression12,$infOperator);


        // 12 < 14 AND test1 == test2
        $andOperator = new core_kernel_classes_Resource(INSTANCE_AND_OPERATOR);
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($trueExpression,$falseExpression,$andOperator);
        $this->assertFalse($finalExpression->evaluate());
        $finalExpression->delete();

        // 12 < 14 AND test1 != test2
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($trueExpression,$trueExpression2,$andOperator);
        $this->assertTrue($finalExpression->evaluate());
        $finalExpression->delete();

        // 12 < 14 OR test1 == test2
        $orOperator = new core_kernel_classes_Resource(INSTANCE_OR_OPERATOR);
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($trueExpression,$falseExpression,$orOperator);
        $this->assertTrue($finalExpression->evaluate());
        $finalExpression->delete();

        // 12 < 14 OR test1 != test2
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($trueExpression,$trueExpression2,$orOperator);
        $this->assertTrue($finalExpression->evaluate());
        $finalExpression->delete();

        // test1 == test2 OR 12 < 14
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($falseExpression,$trueExpression,$orOperator);
        $this->assertTrue($finalExpression->evaluate());
        $finalExpression->delete();

        // test1 == test2 OR 14 < 12
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($falseExpression,$falseExpression2,$orOperator);
        $this->assertFalse($finalExpression->evaluate());
        $finalExpression->delete();


        // (test1 == test2 OR 14 < 12) AND (12 < 14 OR test1 == test2)
        $finalExpression1 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($falseExpression,$falseExpression2,$orOperator);
        $finalExpression2 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($trueExpression,$falseExpression,$orOperator);
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($finalExpression1,$finalExpression2,$andOperator);
        $this->assertFalse($finalExpression->evaluate());
        $finalExpression->delete();

        // (test1 == test2 OR 14 < 12) OR (12 < 14 OR test1 == test2)
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($finalExpression1,$finalExpression2,$orOperator);
        $this->assertTrue($finalExpression->evaluate());
        $finalExpression->delete();

        $finalExpression1->delete();
        $finalExpression2->delete();
        $constantResource1->delete();
        $constantResource2->delete();
        $constantResource12->delete();
        $constantResource12b->delete();
        $terminalExpression1->delete();
        $terminalExpression2->delete();
        $terminalExpression12->delete();
        $terminalExpression12b->delete();
        $terminalExpression14->delete();
        $trueExpression->delete();
        $falseExpression->delete();
        $trueExpression2->delete();
        $falseExpression2->delete();


    }

}