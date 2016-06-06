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
use oat\tao\test\TaoPhpUnitTestRunner;

include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * @author Jerome Bogaerts, <taosupport@tudor.lu>
 * @package tao
 
 */
class OauthTest extends TaoPhpUnitTestRunner {
    
	/**
	 * @var core_kernel_classes_Resource
	 */
	private $credentials;
	
    public function setUp()
    {		
        parent::setUp();
		TaoPhpUnitTestRunner::initTest();
		$class = new core_kernel_classes_Class(	CLASS_OAUTH_CONSUMER);
		$this->credentials = $class->createInstanceWithProperties(array(
			RDFS_LABEL				=> 'test_credentials',
			PROPERTY_OAUTH_KEY		=> 'testcase_12345',
			PROPERTY_OAUTH_SECRET	=> 'secret_12345'
		));
	}
	
	public function tearDown() {
		parent::tearDown();
		$this->credentials->delete();
	}
	
	public function testValidation(){
		// @todo implement curl bassed test
	}
}
?>