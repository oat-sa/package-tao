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

require_once dirname(__FILE__) . '/GenerisTestRunner.php';

class DistributionsTestCase extends TestCasePrototype {
    
	const DIR_SAMPLES = '/samples/distributions/';
	const SAMPLE_DISTRIBUTIONS = 'distributions.php';
	const SAMPLE_DISTRIBUTIONS_MALFORMED = 'malformedDistributions.php';
	const SAMPLE_DISTRIBUTIONS_DOES_NOT_EXIST = 'doesnotexist.php';
	
    public function setUp()
    {
    	parent::setUp();
        GenerisTestRunner::initTest();
    }
    
    public function tearDown(){
    	parent::tearDown();
    }
    
    public function testDistribution(){
    	$id = 'test-distrib';
    	$name = 'Test Distribution';
    	$description = 'Test Distribution Description';
    	$version = '0.1';
    	$extensions = array('fake1', 'fake2');
    	
    	// Test instanciation and getters.
    	$distrib = new common_distrib_Distribution($id, $name, $description, $version, $extensions);
    	$this->assertIsA($distrib, 'common_distrib_Distribution');
    	$this->assertEqual($id, $distrib->getId());
    	$this->assertEqual($name, $distrib->getName());
    	$this->assertEqual($description, $distrib->getDescription());
    	$this->assertEqual($version, $distrib->getVersion());
    	$this->assertEqual($extensions, $distrib->getExtensions());
    	
    	// Test setters.
    	$id = 'test-distrib-2';
    	$name = 'Test Distribution 2';
    	$description = 'Test Distribution Description 2';
    	$version = '0.2';
    	$extensions = array('fake1', 'fake2', 'fake3');
    	
    	$distrib->setId($id);
    	$distrib->setName($name);
    	$distrib->setDescription($description);
    	$distrib->setVersion($version);
    	$distrib->setExtensions($extensions);
    	
    	$this->assertEqual($id, $distrib->getId());
    	$this->assertEqual($name, $distrib->getName());
    	$this->assertEqual($description, $distrib->getDescription());
    	$this->assertEqual($version, $distrib->getVersion());
    	$this->assertEqual($extensions, $distrib->getExtensions());
    	
    	// Misc.
    	$distrib->removeExtension('fake4');
    	$this->assertEqual($extensions, $distrib->getExtensions());
    	$distrib->removeExtension('fake2');
    	$this->assertEqual(array('fake1', 'fake3'), $distrib->getExtensions());
    	$this->assertFalse($distrib->hasExtension('fake2'));
    	$this->assertTrue($distrib->hasExtension('fake1'));
    	$distrib->addExtension('fake2');
    	$this->assertEqual(array('fake1', 'fake3', 'fake2'), $distrib->getExtensions());
    }
    
    public function testManifest(){
    	// Well formed manifest that exists.
    	$file = dirname(__FILE__) . self::DIR_SAMPLES . self::SAMPLE_DISTRIBUTIONS;
    	try{
    		$manifest = new common_distrib_Manifest($file);
    		$this->assertIsA($manifest, 'common_distrib_Manifest');
    		$distribs = $manifest->getDistributions();
    		$this->assertTrue(is_array($distribs));
    		$this->assertEqual(2, count($distribs));
    		$this->assertIsA($distribs[0], 'common_distrib_Distribution');
    		$this->assertIsA($distribs[1], 'common_distrib_Distribution');
    		
    		// Test the content.
    		$distrib = $distribs[0];
    		$this->assertTrue($manifest->getDistributionById('tao-minimal') === $distrib);
    		$this->assertEqual($distrib->getId(), 'tao-minimal');
    		$this->assertEqual($distrib->getName(), 'TAO Minimal Distribution');
    		$this->assertEqual($distrib->getDescription(), 'TAO Minimal Distribution description.');
    		$this->assertEqual($distrib->getVersion(), '2.4');
    		$this->assertEqual($distrib->getExtensions(), array('tao'));
    		
    		$distrib = $distribs[1];
    		$this->assertTrue($manifest->getDistributionById('tao-open-source') === $distrib);
    		$this->assertEqual($distrib->getId(), 'tao-open-source');
    		$this->assertEqual($distrib->getName(), 'TAO Open Source Distribution');
    		$this->assertEqual($distrib->getDescription(), 'TAO Open Source Distribution description.');
    		$this->assertEqual($distrib->getVersion(), '2.4');
    		$this->assertEqual($distrib->getExtensions(), array('tao' ,'filemanager','taoItems','wfEngine','taoResults','taoTests','taoDelivery','taoGroups','taoSubjects', 'wfAuthoring'));
    	}
    	catch (common_distrib_ManifestNotFoundException $e){
    		$this->assertTrue(false, "The manifest should exist in the 'samples' directory.");
    	}
    	catch (common_distrib_MalformedManifestException $e){
    		$this->assertTrue(false, "The manifest should not be malformed.");
    	}
    	catch (common_distrib_DistributionNotFoundException $e){
    		$this->assertTrue(false, "All distributions should be found.");
    	}
    	
    	try{
    		$distrib = $manifest->getDistributionById('funk-music');
    		$this->assertTrue(false, "A common_distrib_DistributionNotFoundException should be thrown.");
    	}
    	catch (common_distrib_DistributionNotFoundException $e){
    		$this->assertTrue(true);
    	}
    	
    	// Try to load a manifest that does not exist.
    	try{
    		$file = dirname(__FILE__) . self::DIR_SAMPLES . self::SAMPLE_DISTRIBUTIONS_DOES_NOT_EXIST;
    		$manifest = new common_distrib_Manifest($file);
    		$this->assertTrue(false, 'The manifest does not exist but was found.');
    	}
    	catch (common_distrib_ManifestNotFoundException $e){
    		$this->assertTrue(true);
    	}
    	
    	// Try to load a manifest that is malformed.
    	try{
    		$file = dirname(__FILE__) . self::DIR_SAMPLES . self::SAMPLE_DISTRIBUTIONS_MALFORMED;
    		$manifest = new common_distrib_Manifest($file);
    		$this->assertTrue(false, 'The manifest should not be readable because mandatory fields are missing.');
    	}
    	catch (common_distrib_MalformedManifestException $e){
    		$this->assertTrue(true);
    	}
    }
}