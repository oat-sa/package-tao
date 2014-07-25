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

require_once dirname(__FILE__) . '/GenerisTestRunner.php';

/**

/**
 * Test class for Expression.
*/

class UtilsTestCase extends UnitTestCase {
	
	
	public function testIsUri(){
		$toto = 'http://localhost/middleware/Rules.rdf#i122044076930844';
		$toto2 = 'j ai super fain';
		$toto3 = 'http://localhost/middleware/Rules.rdf';
		$this->assertTrue(common_Utils::isUri($toto));
		$this->assertFalse(common_Utils::isUri($toto2));
		$this->assertFalse(common_Utils::isUri($toto3));
	}
	
	public function testGetNewUri(){
		$toto = common_Utils::getNewUri();
		$tata = common_Utils::getNewUri();
		$this->assertNotEqual($toto,$tata);
	}
	

}
?>