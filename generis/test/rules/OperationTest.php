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

class OperationTest extends GenerisPhpUnitTestRunner {


	public function setUp(){
		GenerisPhpUnitTestRunner::initTest();
	}
	
	public function testEvaluate(){
		$constant5 = core_kernel_rules_TermFactory::createConst('5');
		$constant12 = core_kernel_rules_TermFactory::createConst('12');
		
		//5 + 12
		$operation = core_kernel_rules_OperationFactory::createOperation(
			$constant5,
			$constant12,
			new core_kernel_classes_Resource(INSTANCE_OPERATOR_ADD)
		);
		$result = $operation->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEquals ($result->literal,'17');
		
		//5 - 12
		$operation = core_kernel_rules_OperationFactory::createOperation(
			$constant5,
			$constant12,
			new core_kernel_classes_Resource(INSTANCE_OPERATOR_MINUS)
		);
		$result = $operation->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEquals ($result->literal,'-7');
		
		//5 * 12
		$operation = core_kernel_rules_OperationFactory::createOperation(
			$constant5,
			$constant12,
			new core_kernel_classes_Resource(INSTANCE_OPERATOR_MULTIPLY)
		);
		$result = $operation->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEquals ($result->literal,'60');
		
		//60 / 12
		$constant60 = core_kernel_rules_TermFactory::createConst('60');		
		$operation = core_kernel_rules_OperationFactory::createOperation(
			$constant60,
			$constant12,
			new core_kernel_classes_Resource(INSTANCE_OPERATOR_DIVISION)
		);
		$result = $operation->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEquals ($result->literal,'5');
		
		// 60 concat 12 
		$operation = core_kernel_rules_OperationFactory::createOperation(
			$constant60,
			$constant12,
			new core_kernel_classes_Resource(INSTANCE_OPERATOR_CONCAT)
		);
		$result = $operation->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEquals ($result->literal,'60 12');
		
		// raise excption bad operator
		$operation = core_kernel_rules_OperationFactory::createOperation(
			$constant60,
			$constant12,
			new core_kernel_classes_Resource(INSTANCE_OPERATOR_UNION)
		);
		
		try {
			$operation->evaluate();
			$this->fail('should raise exception : problem evaluating operation, operator do not match with operands');
		} catch (common_Exception $e) {
			$this->assertEquals ($e->getMessage(),'problem evaluating operation, operator do not match with operands');
		}

		
		
		$constant60->delete();
		$constant5->delete();
		$constant12->delete();
		$operation->delete();
	}

}