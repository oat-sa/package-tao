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
?>
<?php
error_reporting(E_ALL);
require_once dirname(__FILE__) . '/../../../tao/test/TaoTestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class TermTestCase extends UnitTestCase {


	public function setUp(){
		TaoTestRunner::initTest();
	}
	
	public function testEvaluate(){
		
		
		//bad term
		$badTermResource = core_kernel_classes_ResourceFactory::create(
				new core_kernel_classes_Class(CLASS_TERM),
				'bad term',
				__METHOD__);
		$badTerm = new core_kernel_rules_Term($badTermResource->getUri());
		try {
			$badTerm->evaluate();
			$this->fail('should raise exception : Forbidden type');
		} catch (common_Exception $e) {
			$this->assertEqual($e->getMessage(),'Forbidden Type of Term');
		}
		
		// eval const
		$constantResource = core_kernel_rules_TermFactory::createConst('test1');
		$term = $constantResource->evaluate();			
		$this->assertIsA($term,'core_kernel_classes_Literal');
		$this->assertEqual($term,'test1');
		$constantResource->delete();
		
		//eval SPX
		$booleanClass = new core_kernel_classes_Class(GENERIS_BOOLEAN);
		
		$maybe = core_kernel_classes_ResourceFactory::create($booleanClass, 'testCase testCreateSPX',__METHOD__);
		$SPXResource = core_kernel_rules_TermFactory::createSPX($maybe,new core_kernel_classes_Property(RDFS_COMMENT));
		$spxResult = $SPXResource->evaluate();
		$this->assertIsA($spxResult,'core_kernel_classes_Literal');
		$this->assertEqual($spxResult,__METHOD__);
		
		// eval operation
		$constant5 = core_kernel_rules_TermFactory::createConst('5');
		$constant12 = core_kernel_rules_TermFactory::createConst('12');
		$operation = core_kernel_rules_OperationFactory::createOperation(
				$constant5,
				$constant12,
				new core_kernel_classes_Resource(INSTANCE_OPERATOR_ADD)
		);
		$operationTerm = new core_kernel_rules_Term($operation->getUri());
		$result = $operationTerm->evaluate();
		$this->assertEqual($result->literal,'17');
		
		
		$fakeTerm = new core_kernel_rules_Term($maybe->getUri());
		try {
			$fakeTerm->evaluate();
			$this->fail('should raise exception : Forbidden type');	
		} catch (common_Exception $e) {
			$this->assertEqual($e->getMessage(),'problem evaluating Term');
		}
		
		$badTermResource->delete();
		$constant5->delete();
		$constant12->delete();
		$SPXResource->delete();
		$operation->delete();
		$maybe->delete();

	}
	

	

}