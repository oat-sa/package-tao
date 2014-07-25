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

class ConfigurationTestCasePrototype extends TestCasePrototype {
	
	const TESTKEY = 'config_test_key';
    
    public function setUp()
    {
        GenerisTestRunner::initTest();
    }
    
    public function testPHPIniValues(){
        // core tests.
        $ini = new common_configuration_PHPINIValue('text/html', 'default_mimetype');
        $this->assertEqual($ini->getName(), 'default_mimetype');
        $this->assertEqual($ini->isOptional(), false);
        $this->assertEqual($ini->getExpectedValue(), 'text/html');
        $ini->setOptional(true);
        $this->assertEqual($ini->isOptional(), true);
        $ini->setName('foobar');
        $this->assertEqual($ini->getName(), 'foobar');
        $ini->setExpectedValue('text/xml');
        $this->assertEqual($ini->getExpectedValue(), 'text/xml');
        
        // String INI Option test.
        $ini->setName('default_mimetype');
        $ini->setExpectedValue('text/html');
        $oldIniValue = ini_get($ini->getName());
        ini_set($ini->getName(), 'text/html');
        $report = $ini->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        $this->assertEqual($report->getStatusAsString(), 'valid');
        
        ini_set($ini->getName(), 'text/xml');
        $report = $ini->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::INVALID);
        $this->assertEqual($report->getStatusAsString(), 'invalid');
        
        $ini->setName('foobar');
        $report = $ini->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::UNKNOWN);
        $this->assertEqual($report->getStatusAsString(), 'unknown');
        
        ini_set($ini->getName(), $oldIniValue);
    }

    function testPHPRuntime() {
        // Core tests.
        $php = new common_configuration_PHPRuntime('5.3', '5.4');
        $this->assertEqual($php->getName(), 'tao.configuration.phpruntime'); //automatically set
        $this->assertEqual($php->getMin(), '5.3');
        $this->assertEqual($php->getMax(), '5.4');
        $this->assertFalse($php->isOptional());
        
        $php->setMin('5.2');
        $this->assertEqual($php->getMin(), '5.2');
        
        $php->setMax('5.3');
        $this->assertEqual($php->getMax(), '5.3');
        
        $php->setName('foobar');
        $this->assertEqual($php->getName(), 'foobar');
        
        $php->setOptional(true);
        $this->assertTrue($php->isOptional());
        
        // max & min test.
        $php = new common_configuration_PHPRuntime('5.3', '5.5');
        $report = $php->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        $php->setMin('5.5');
        $php->setMax('5.5.6.3');
        $report = $php->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::INVALID);
        
        // min test.
        $php = new common_configuration_PHPRuntime('5.3', null);
        $report = $php->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        $php->setMin('5.5.3');
        $report = $php->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::INVALID);
        
        // max test.
        $php = new common_configuration_PHPRuntime(null, '5.5');
        $report = $php->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        $php->setMax('5.2');
        $this->assertEqual($php->getMax(), '5.2');
        $report = $php->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::INVALID);
        
        $php->setMax('5.3.x');
        $this->assertEqual($php->getMax(), '5.3.99999');
    }

    function testPHPExtension(){
        // Testing PHPExtension existence and version is quite hard to do. Indeed,
        // it depends what the installed extensions are on the computers running the tests.
        // Thus, I decided to use the DOM extension as a test. I've never seen a PHP installation
        // without the DOM extension. It comes built-in except if you compile PHP with the
        // --disable-dom directive.
        
        // core tests.
        $ext = new common_configuration_PHPExtension(null, null, 'dom');
        $this->assertEqual($ext->getMin(), null);
        $this->assertEqual($ext->getMax(), null);
        $this->assertEqual($ext->getName(), 'dom');
        $this->assertEqual($ext->isOptional(), false);
        
        $ext->setMin('1.0');
        $this->assertEqual($ext->getMin(), '1.0');
        
        $ext->setMax('2.0');
        $this->assertEqual($ext->getMax(), '2.0');
        
        $ext->setName('foobar');
        $this->assertEqual($ext->getName(), 'foobar');
        
        // test with an extension that has no version information (hash) that
        // contains hash functions such as md5().
        // We consider that if there is no version information but the extension
        // is loaded, the report is always valid even if min and/or max version(s)
        // are specified.
        $ext = new common_configuration_PHPExtension(null, null, 'hash');
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        $ext->setMin('1.0');
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        $ext->setMax('2.0');
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        $ext->setMin(null);
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        // test with the dom extension that has version information.
        $ext = new common_configuration_PHPExtension('19851127', '20090601', 'dom');
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        $ext->setMin('20050112');
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::INVALID);
        
        $ext->setMin(null);
        $ext->setMax('20020423');
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::INVALID);
        
        $ext->setMin('20010731');
        $ext->setMax(null);
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        // Unexisting extension.
        $ext = new common_configuration_PHPExtension('1.0', '1.4', 'foobar');
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::UNKNOWN);
    }  

    public function testFileSystemComponent(){
        $f = new common_configuration_FileSystemComponent(__FILE__, 'r');
        $this->assertEqual($f->getLocation(), __FILE__);
        $this->assertEqual($f->getExpectedRights(), 'r');
        $this->assertEqual($f->getName(), 'tao.configuration.filesystem'); // automatically set.
        $this->assertFalse($f->isOptional());
        $this->assertTrue($f->isReadable());
        
        $this->expectException(new common_configuration_MalformedRightsException("Malformed rights. Expected format is r|rw|rwx."));
        $f->setExpectedRights('fail');
        
        try{
            $f->setExpectedRights('rw');
            $this->pass();
        }
        catch (common_configuration_MalformedRightsException $e){
            $this->fail();
        }
    }
    
    public function testSimpleComponentCollection(){
    	// Non acyclic simple test.
    	$collection = new common_configuration_ComponentCollection();
    	$componentA = new common_configuration_Mock(common_configuration_Report::VALID, 'componentA');
    	$componentB = new common_configuration_Mock(common_configuration_Report::VALID, 'componentB');
    	$componentC = new common_configuration_Mock(common_configuration_Report::VALID, 'componentC');
    	
    	$collection->addComponent($componentA);
    	$collection->addComponent($componentB);
    	$collection->addComponent($componentC);
    	
    	$collection->addDependency($componentC, $componentA);
    	$collection->addDependency($componentB, $componentA);
    	
    	try{
    		$reports = $collection->check();
    		$this->assertTrue(true); // The graph is acyclic. Perfect!
    		$this->assertEqual(count($collection->getCheckedComponents()), 3);
    		$this->assertEqual(count($collection->getUncheckedComponents()), 0);
    		$this->assertEqual(count($reports), 3);
    		
    		// Make 'componentB' silent.
    		$collection->silent($componentB);
    		$reports = $collection->check();
    		$this->assertEqual(count($reports), 2);
    		$this->assertEqual(count($collection->getCheckedComponents()), 3);
    		
    		// Make 'componentB' noisy again.
    		$collection->noisy($componentB);
    		$reports = $collection->check();
    		$this->assertEqual(count($reports), 3);
    		
    		// Now change some reports validity.
    		$componentA->setExpectedStatus(common_configuration_Report::INVALID);
    		
    		$reports = $collection->check();
    		$this->assertTrue(true); // Acyclic graph, no CyclicDependencyException thrown.
    		$this->assertEqual(count($reports), 1);
    		$this->assertEqual($collection->getCheckedComponents(), array($componentA));
    		$this->assertEqual($collection->getUncheckedComponents(), array($componentB, $componentC));
    		
    		// Add an additional independant component.
    		$componentD = new common_configuration_Mock(common_configuration_Report::VALID, 'componentD');
    		$collection->addComponent($componentD);
    		$reports = $collection->check();
    		$this->assertTrue(true); // Acyclic graph, no CyclicDependencyException thrown.
    		$this->assertEqual(count($reports), 2);
    		$this->assertEqual($collection->getCheckedComponents(), array($componentA, $componentD));
    		$this->assertEqual($collection->getUncheckedComponents(), array($componentB, $componentC));
    		
    		// Make the whole components valid again.
    		$componentA->setExpectedStatus(common_configuration_Report::VALID);
    		$reports = $collection->check();
    		$this->assertTrue(true); // Acyclic graph, no CyclicDependencyException thrown.
    		$this->assertEqual(count($reports), 4);
    		$this->assertEqual($collection->getCheckedComponents(), array($componentA, $componentB, $componentC, $componentD));
    		$this->assertEqual($collection->getUncheckedComponents(), array());
    		
    		try{
    			// Make the graph cyclic.
    			$collection->addDependency($componentA, $componentB);
    			$collection->check();
    			$this->assertTrue(false, 'The graph should be cyclic.');
    		}
    		catch (common_configuration_CyclicDependencyException $e){
    			$this->assertTrue(true);
    			$this->assertEqual($collection->getReports(), array());
    			$this->assertEqual($collection->getCheckedComponents(), array());
    			$this->assertEqual($collection->getUncheckedComponents(), array($componentA, $componentB, $componentC, $componentD));
    			
    			// Finally, we test a ComponentCollection reset.
    			$collection->reset();
    			$this->assertEqual($collection->getReports(), array());
    			$this->assertEqual($collection->getCheckedComponents(), array());
    			$this->assertEqual($collection->getUncheckedComponents(), array());
    			$reports = $collection->check();
    			$this->assertEqual($reports, array());
    			$this->assertEqual($collection->getReports(), array());
    			$this->assertEqual($collection->getCheckedComponents(), array());
    			$this->assertEqual($collection->getUncheckedComponents(), array());
    		}
    	}
    	catch (common_configuration_CyclicDependencyException $e){
    		$this->assertTrue(false, 'The graph dependency formed by the ComponentCollection must be acyclic.');
    	}
    }
    
    public function testComponentFactory(){
    	$component = common_configuration_ComponentFactory::buildPHPRuntime('5.0', '5.5', true);
    	$this->assertIsA($component, 'common_configuration_PHPRuntime');
    	$this->assertEqual($component->getMin(), '5.0');
    	$this->assertEqual($component->getMax(), '5.5');
    	$this->assertTrue($component->isOptional());
    	$report = $component->check();
    	$this->assertIsA($report, 'common_configuration_Report');
    	$this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
    	
    	$component = common_configuration_ComponentFactory::buildPHPExtension('spl');
    	$this->assertIsA($component, 'common_configuration_PHPExtension');
    	$this->assertNull($component->getMin());
    	$this->assertNull($component->getMax());
    	$this->assertEqual($component->getName(), 'spl');
    	$this->assertFalse($component->isOptional());
    	$report = $component->check();
    	$this->assertIsA($report, 'common_configuration_Report');
    	$this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
    	
    	$component = common_configuration_ComponentFactory::buildPHPExtension('spl', null, null, true);
    	$this->assertIsA($component, 'common_configuration_PHPExtension');
    	$this->assertEqual($component->getName(), 'spl');
    	$this->assertTrue($component->isOptional());
    	$report = $component->check();
    	$this->assertIsA($report, 'common_configuration_Report');
    	$this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
    	
    	$component = common_configuration_ComponentFactory::buildPHPINIValue('magic_quotes_gpc', '0');
    	$this->assertIsA($component, 'common_configuration_PHPINIValue');
    	$this->assertEqual($component->getName(), 'magic_quotes_gpc');
    	$this->assertFalse($component->isOptional());
    	
    	$component = common_configuration_ComponentFactory::buildPHPDatabaseDriver('pdo_mysql', true);
    	$this->assertIsA($component, 'common_configuration_PHPDatabaseDriver');
    	$this->assertEqual($component->getName(), 'pdo_mysql');
    	$this->assertTrue($component->isOptional());
    	$report = $component->check();
    	$this->assertIsA($report, 'common_configuration_Report');
    	$this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
    	
    	$location = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ConfigurationTestCase.php';
    	$component = common_configuration_ComponentFactory::buildFileSystemComponent($location, 'r');
    	$this->assertIsA($component, 'common_configuration_FileSystemComponent');
    	$this->assertFalse($component->isOptional());
    	$this->assertEqual($component->getLocation(), $location);
    	$this->assertEqual($component->getExpectedRights(), 'r');
    	$report = $component->check();
    	$this->assertIsA($report, 'common_configuration_Report');
    	$this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
    	
    	// TAO Dependency
    	/*$component = common_configuration_ComponentFactory::buildCustom('database_drivers', 'tao', false);
    	$this->assertIsA($component, 'common_configuration_Component');
    	$this->assertEqual($component->getName(), 'custom_tao_DatabaseDrivers');
    	$this->assertFalse($component->isOptional());*/
    	
    	
    	$array = array('type' => 'PHPRuntime', 'value' => array('min' => '5.0', 'max' => '5.5', 'optional' => true));
    	$component = common_configuration_ComponentFactory::buildFromArray($array);
    	$this->assertIsA($component, 'common_configuration_PHPRuntime');
    	$this->assertEqual($component->getMin(), '5.0');
    	$this->assertEqual($component->getMax(), '5.5');
    	$this->assertTrue($component->isOptional());
    	$report = $component->check();
    	$this->assertIsA($report, 'common_configuration_Report');
    	$this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
    	
    	$array = array('type' => 'PHPExtension', 'value' => array('name' => 'spl'));
    	$component = common_configuration_ComponentFactory::buildFromArray($array);
    	$this->assertIsA($component, 'common_configuration_PHPExtension');
    	$this->assertEqual($component->getName(), 'spl');
    	$this->assertFalse($component->isOptional());
    	$report = $component->check();
    	$this->assertIsA($report, 'common_configuration_Report');
    	$this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
    	
    	// 'Check' before the Component type is accepted.
    	$array = array('type' => 'CheckPHPINIValue', 'value' => array('name' => 'magic_quotes_gpc', 'value' => '0'));
    	$component = common_configuration_ComponentFactory::buildFromArray($array);
    	$this->assertIsA($component, 'common_configuration_PHPINIValue');
    	$this->assertEqual($component->getName(), 'magic_quotes_gpc');
    	$this->assertFalse($component->isOptional());
    	
    	$array = array('type' => 'PHPDatabaseDriver', 'value' => array('name' => 'pdo_mysql', 'optional' => true));
    	$component = common_configuration_ComponentFactory::buildFromArray($array);
    	$this->assertIsA($component, 'common_configuration_PHPDatabaseDriver');
    	$this->assertEqual($component->getName(), 'pdo_mysql');
    	$this->assertTrue($component->isOptional());
    	$report = $component->check();
    	$this->assertIsA($report, 'common_configuration_Report');
    	$this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
    	
    	$array = array('type' => 'FileSystemComponent', 'value' => array('location' => $location, 'rights' => 'r'));
    	$component = common_configuration_ComponentFactory::buildFromArray($array);
    	$this->assertIsA($component, 'common_configuration_FileSystemComponent');
    	$this->assertFalse($component->isOptional());
    	$this->assertEqual($component->getLocation(), $location);
    	$this->assertEqual($component->getExpectedRights(), 'r');
    	$report = $component->check();
    	$this->assertIsA($report, 'common_configuration_Report');
    	$this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
    	
    	// TAO Dependency
    	/*
    	$array = array('type' => 'Custom', 'value' => array('name' => 'database_drivers', 'extension' => 'tao', 'optional' => false));
    	$component = common_configuration_ComponentFactory::buildFromArray($array);
    	$this->assertIsA($component, 'common_configuration_Component');
    	$this->assertEqual($component->getName(), 'custom_tao_DatabaseDrivers');
    	$this->assertFalse($component->isOptional());
    	
    	try{
    		$array = array('type' => 'Custom', 'value' => array('name' => 'mod_rewrite'));
    		$component = common_configuration_ComponentFactory::buildFromArray($array);
    		$this->assertTrue(false, 'An exception should be raised because mod_rewrite check does not exist for default extension generis.');
    	}
    	catch (common_configuration_ComponentFactoryException $e){
    		$this->assertTrue(true);
    	}
    	*/
    	
    	try{
    		$array = array('type' => 'PHPINIValue', 'value' => array());
    		$component = common_configuration_ComponentFactory::buildFromArray($array);
    		$this->assertTrue(false, 'An exception should be raised because of missing attributes.');
    	}
    	catch (common_configuration_ComponentFactoryException $e){
    		$this->assertTrue(true);
    	}
    }
    
	public function testUserConfig()
	{
		$generis = common_ext_ExtensionsManager::singleton()->getExtensionById('generis');
		
		$this->assertNull($generis->getConfig(self::TESTKEY));
		
		$random = rand(0, 999999);
		$generis->setConfig(self::TESTKEY, $random);
		$this->assertEqual($generis->getConfig(self::TESTKEY), $random);
		
		$generis->unsetConfig(self::TESTKEY);
		$this->assertNull($generis->getConfig(self::TESTKEY));
		
	}
}