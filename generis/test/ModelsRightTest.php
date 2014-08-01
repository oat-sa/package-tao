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
require_once dirname(__FILE__) . '/GenerisPhpUnitTestRunner.php';


class ModelsRightTest extends GenerisPhpUnitTestRunner {
	
	public function setUp(){
        GenerisPhpUnitTestRunner::initTest();
	}
	
	public function testRightModels(){
		// In tao context, the only one model which is updatable
		$updatableModels = core_kernel_persistence_smoothsql_SmoothModel::getReadableModelIds();
		$this->assertEquals(count($updatableModels), 1);
		$this->assertTrue(array_search(LOCAL_NAMESPACE, $updatableModels) !== false);
		
		// Try to delete a resource of a locked model
		$property = new core_kernel_classes_Property(RDFS_LABEL);
        $domain = new core_kernel_classes_Property(RDFS_DOMAIN, __METHOD__);
		$this->assertFalse( $property->removePropertyValues($domain, array('pattern' => RDFS_LABEL)));
		
		
		// Try to remove a property value which is lg dependent of a locked model
		$clazz = new core_kernel_classes_Class('http://www.tao.lu/middleware/Rules.rdf#And');
		$this->assertFalse ($clazz->removePropertyValueByLg($property, 'EN'));
	}
}