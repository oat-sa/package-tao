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

class TermFactoryTest extends GenerisPhpUnitTestRunner {


	public function setUp(){
		GenerisPhpUnitTestRunner::initTest();
	}
	
	public function testCreateConst(){
		$constantResource = core_kernel_rules_TermFactory::createConst('test1');
		$this->assertIsA($constantResource,'core_kernel_rules_Term');
		$typeUri = array_keys($constantResource->getTypes());
		$this->assertEquals($typeUri[0],CLASS_TERM_CONST);
		$this->assertTrue(count($typeUri) == 1);
			
		$termValueProperty = new core_kernel_classes_Property(PROPERTY_TERM_VALUE);
		$logicalOperatorProperty = new core_kernel_classes_Property(PROPERTY_HASLOGICALOPERATOR);
		$terminalExpressionProperty = new core_kernel_classes_Property(PROPERTY_TERMINAL_EXPRESSION);
		
		$term = $constantResource->getUniquePropertyValue($termValueProperty);
		$this->assertIsA($term,'core_kernel_classes_Literal');
		$this->assertEquals($term,'test1');
		
		$operator = $constantResource->getUniquePropertyValue($logicalOperatorProperty);
		$this->assertIsA($operator,'core_kernel_classes_Resource');
		$this->assertEquals($operator->getUri(),INSTANCE_EXISTS_OPERATOR_URI);
	
		$terminalExpression = $constantResource->getUniquePropertyValue($terminalExpressionProperty);
		$this->assertIsA($terminalExpression,'core_kernel_classes_Resource');
		$this->assertEquals($terminalExpression->getUri(),$constantResource->getUri());

		$constantResource->delete();
	}
	
	public function testCreateSPX(){
		$booleanClass = new core_kernel_classes_Class(GENERIS_BOOLEAN);
		$maybe = core_kernel_classes_ResourceFactory::create($booleanClass, 'testCase testCreateSPX',__METHOD__);
		
		$SPXResource = core_kernel_rules_TermFactory::createSPX($maybe,new core_kernel_classes_Property(RDFS_COMMENT));
		$this->assertIsA($SPXResource,'core_kernel_rules_Term');
		
		$subjectProperty = new core_kernel_classes_Property(PROPERTY_TERM_SPX_SUBJET);
		$predicateProperty = new core_kernel_classes_Property(PROPERTY_TERM_SPX_PREDICATE);
     	
		$subject = $SPXResource->getUniquePropertyValue($subjectProperty);
     	$this->assertIsA($subject,'core_kernel_classes_Resource');
		$this->assertEquals($subject->getUri(),$maybe->getUri());
     	
     	$predicate = $SPXResource->getUniquePropertyValue($predicateProperty);
		$this->assertIsA($predicate,'core_kernel_classes_Resource');
		$this->assertEquals($predicate->getUri(),RDFS_COMMENT);
		
		$SPXResource->delete();
		$maybe->delete();
	}
	

}