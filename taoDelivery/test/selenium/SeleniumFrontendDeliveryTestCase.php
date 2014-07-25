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
require_once(dirname(__FILE__) . '/../../../tao/test/selenium/includes/FunctionalTestCase.class.php');
require_once(dirname(__FILE__) . '/includes/BehaviourFramework.php');
require_once(dirname(__FILE__) . '/../../includes/raw_start.php');

class SeleniumFrontendDeliveryTestCase extends FunctionalTestCase {

	public function setUp()
    {
		parent::setUp();
    }
 
    public function testDelivery()
    {
    	$this->openLocation('frontend');
    	$this->type("//input[@id='login']", 'taker1');
    	$this->type("//input[@id='password']", 'taker1');
    	$this->clickAndWait("//input[@id='connect']");
    	
    	// We arrive on the main menu. Create a new process, the first
    	// type in the available processes list.
    	$this->assertElementPresent("//h1[@id='welcome_message']");
    	$this->assertElementPresent("//div[@id='new_process']/ul/li[1]");
    	$this->clickAndWait("//div[@id='new_process']/ul/li[1]/a");
    	
    	// The process is now launched and the user ready to take the first
    	// item of the process. Let's randomly answer to each of them.
    	$inItem = true;
    	while ($inItem) {
    		$this->assertElementPresent('//iframe[1]');
    		$this->selectFrame('//iframe[1]');
    		
    		$itemBehaviour = new QTIBehaviour($this);
    		$itemBehaviour->run();
    		
    		if($this->isElementPresent("//h1[@id='welcome_message']")) {
    			$inItem = false;
    		}
    	}
    }
}
?>