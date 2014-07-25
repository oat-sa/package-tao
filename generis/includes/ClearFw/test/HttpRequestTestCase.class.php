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
 * Copyright (c) 2006-2009 (original work) Public Research Centre Henri Tudor (under the project FP6-IST-PALETTE);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

/**
 * 
 */
class HttpRequestTestCase extends UnitTestCase  {
    
    /**
     * Return module name
     * @return	string		Module
     */
    function testGetModule() {
    	// test 1
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php/essai/test/";
   		$httpRequest	= new HttpRequest();	
    	$this->assertTrue($httpRequest->getModule(), "essai");
    	
    	// test 2
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php/";
   		$httpRequest	= new HttpRequest();	
    	$this->assertNull($httpRequest->getModule());
    	
    	// test 3
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php?variable=1";
   		$httpRequest	= new HttpRequest();	
    	$this->assertNull($httpRequest->getModule());
    	
    	// test 4
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php/module/essai?variable=1";
   		$httpRequest	= new HttpRequest();	
    	$this->asserttrue($httpRequest->getModule(), "module");
    }
    
    /**
     * Return action name
     * @return	string		Action
     */
    function testGetAction() {
    	// test 1
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php/essai/test/";
   		$httpRequest	= new HttpRequest();	
    	$this->assertTrue($httpRequest->getAction(), "test");
    	
    	// test 2
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php/";
   		$httpRequest	= new HttpRequest();
    	$this->assertNull($httpRequest->getAction());
    	
    	// test 3
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php?variable=1";
   		$httpRequest	= new HttpRequest();	
    	$this->assertNull($httpRequest->getAction());
    	
    	// test 4
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php/module/essai?variable=1";
   		$httpRequest	= new HttpRequest();	
    	$this->asserttrue($httpRequest->getAction(), "essai");
    }
    
    /**
     * Return arguments
     * @return	array		Arguments
     */
    function testGetArgs() {
   		$httpRequest	= new HttpRequest();
   		$args = $httpRequest->getArgs();	
    	$this->asserttrue(empty($args));
    }
    
    /**
     * Return an argument
     * @param	string		$pKey		Argument name
     * @return	string		Argument value
     */
    function testGetArgument() {
    	
    }
}
?>