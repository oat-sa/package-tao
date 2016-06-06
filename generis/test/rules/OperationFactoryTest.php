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

class OperationFactoryTestCase extends GenerisPhpUnitTestRunner {


	public function setUp(){
		GenerisPhpUnitTestRunner::initTest();
	}
	
	public function testCreateOperation(){
		$constant5 = core_kernel_rules_TermFactory::createConst('5');
		$constant12 = core_kernel_rules_TermFactory::createConst('12');
		$operation = core_kernel_rules_OperationFactory::createOperation(
			$constant5,
			$constant12,
			new core_kernel_classes_Resource(INSTANCE_OPERATOR_ADD)
		);
		$this->assertIsA($operation,'core_kernel_rules_Operation');
		
		$firstOperand = new core_kernel_classes_Property(PROPERTY_OPERATION_FIRST_OP);
		$secondOperand = new core_kernel_classes_Property(PROPERTY_OPERATION_SECND_OP);	
		$operatorProperty = new core_kernel_classes_Property(PROPERTY_OPERATION_OPERATOR);
		
		$operator = $operation->getUniquePropertyValue($operatorProperty);
		$this->assertIsA($operator,'core_kernel_classes_Resource');
		$this->assertEquals($operator->getUri(),INSTANCE_OPERATOR_ADD);
		
        $term1 = $operation->getUniquePropertyValue($firstOperand);
        $this->assertIsA($term1,'core_kernel_classes_Resource');
		$this->assertEquals($term1->getUri(),$constant5->getUri());
        
		$term2 = $operation->getUniquePropertyValue($secondOperand);
		$this->assertIsA($term2,'core_kernel_classes_Resource');
		$this->assertEquals($term2->getUri(),$constant12->getUri());
		
		$constant5->delete();
		$constant12->delete();
		$operation->delete();
	}

}