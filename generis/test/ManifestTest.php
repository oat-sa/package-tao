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

class ManifestTest extends GenerisPhpUnitTestRunner {
	
	const SAMPLES_PATH = '/../test/samples/manifests/';
	const MANIFEST_PATH_DOES_NOT_EXIST = 'idonotexist.php';
	const MANIFEST_PATH_LIGHTWEIGHT = 'lightweightManifest.php';
	const MANIFEST_PATH_COMPLEX = 'complexManifest.php';
	
	public function setUp(){
        GenerisPhpUnitTestRunner::initTest();
	}
	
	public function testManifestLoading(){
		$currentPath = dirname(__FILE__);
		
		// try to load a manifest that does not exists.
		try{
			$manifestPath = $currentPath . self::SAMPLES_PATH . self::MANIFEST_PATH_DOES_NOT_EXIST;
			$manifest = new common_ext_Manifest($manifestPath);
			$this->assertTrue(false, "Trying to load a manifest that does not exist should raise an exception");
		}
		catch (Exception $e){
			$this->assertIsA($e, 'common_ext_ManifestNotFoundException');
		}
		
		// Load a simple lightweight manifest that exists and is well formed.
		$manifestPath = $currentPath . self::SAMPLES_PATH . self::MANIFEST_PATH_LIGHTWEIGHT;
		try{
			$manifest = new common_ext_Manifest($manifestPath);
			$this->assertIsA($manifest, 'common_ext_Manifest');
			$this->assertEquals($manifest->getName(), 'lightweight');
			$this->assertEquals($manifest->getDescription(), 'lightweight testing manifest');
			$this->assertEquals($manifest->getVersion(), '1.0');
			$this->assertEquals($manifest->getAuthor(), 'TAO Team');
		}
		catch (common_ext_ManifestException $e){
			$this->assertTrue(false, "Trying to load a manifest that exists and well formed should not raise an exception.");
		}
		
		// Load a more complex manifest that exists and is well formed.
		$manifestPath = $currentPath . self::SAMPLES_PATH . self::MANIFEST_PATH_COMPLEX;
		try{
			$manifest = new common_ext_Manifest($manifestPath);
			$this->assertIsA($manifest, 'common_ext_Manifest');
			$this->assertEquals($manifest->getName(), 'complex');
			$this->assertEquals($manifest->getDescription(), 'complex testing manifest');
			$this->assertEquals($manifest->getVersion(), '1.0');
			$this->assertEquals($manifest->getAuthor(), 'TAO Team');
			$this->assertEquals(array_keys($manifest->getDependencies()), array('taoItemBank', 'taoDocuments'));
			$this->assertEquals($manifest->getInstallModelFiles(), array('/extension/path/models/ontology/taofuncacl.rdf',
																  		'/extension/path/models/ontology/taoitembank.rdf'));
			$this->assertEquals($manifest->getConstants(), array('WS_ENDPOINT_TWITTER' => 'http://twitter.com/statuses/', 'WS_ENDPOINT_FACEBOOK' => 'http://api.facebook.com/restserver.php'));
			$this->assertEquals($manifest->getOptimizableClasses(), array('http://www.linkeddata.org/ontologies/data.rdf#myClass1','http://www.linkeddata.org/ontologies/data.rdf#myClass2'));
			$this->assertEquals($manifest->getOptimizableProperties(), array('http://www.linkeddata.org/ontologies/props.rdf#myProp1','http://www.linkeddata.org/ontologies/props.rdf#myProp2'));
			
		}
		catch (common_ext_ManifestException $e){
			$this->assertTrue(false, $e->getMessage());
		}
		
		// Load a malformed manifest.
		// @TODO try to load a malformed manifest.
	}
}