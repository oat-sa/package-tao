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

/**
 * Test of the common_ext_Namespace and common_ext_NamesapceManager
 * 
 * @author Bertrand Chevrier <bertrand.chevrier@tudor.lu>
 * @package generis
 
 */
class NamespaceTest extends GenerisPhpUnitTestRunner {
	
	public function setUp(){
        GenerisPhpUnitTestRunner::initTest();
	}

	/**
	 * Tes if the model is correctly loaded, especially the manager singleton
	 */
	public function testModel(){
		$namespaceManager = common_ext_NamespaceManager::singleton();
		$this->assertIsA($namespaceManager, 'common_ext_NamespaceManager');
		
		//$this->assertReference($namespaceManager, common_ext_NamespaceManager::singleton());
		
		$tempNamesapce = new common_ext_Namespace();
		$this->assertIsA($tempNamesapce, 'common_ext_Namespace');
	}
	
	/**
	 * test the manager retrieving methods and the namespace setters/getters
	 */
	public function testBehaviour(){
		$namespaceManager = common_ext_NamespaceManager::singleton();
		$namespaces = $namespaceManager->getAllNamespaces();
		$this->assertTrue(count($namespaces) > 0);
		
		foreach($namespaces as $namespace){
			$this->assertIsA($namespace, 'common_ext_Namespace');
		}
		
		$localNs = $namespaceManager->getLocalNamespace();
		$this->assertIsA($localNs, 'common_ext_Namespace');

		$otherLocalNs = $namespaceManager->getNamespace($localNs->getModelId());
		$this->assertIsA($otherLocalNs, 'common_ext_Namespace');
		
		$this->assertEquals((string)$otherLocalNs, (string)$localNs);
	}
	
}