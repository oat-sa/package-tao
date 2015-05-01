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

error_reporting(E_ALL);

use oat\generis\test\GenerisPhpUnitTestRunner;
use Prophecy\Prophet;

/**

/**
 * Test class for Expression.
*/

class generis_test_UtilsTest extends GenerisPhpUnitTestRunner {
	
	
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
		
		$this->assertNotSame($toto,$tata);
		
		
		
		
	}
	
	public function testPhpStringEscaping(){
	    
	    // normal chars
	    $utf = 'ÆýЉϿϫ˲ ˦ˈŒ\'"\\\\';
	    $value = eval("return ".common_Utils::toPHPVariableString($utf).";");
	    $this->assertEquals($utf,$value);

        // test binary safe
        $binaryString = $this->buildBinString();
        $value = eval("return ".common_Utils::toPHPVariableString($binaryString).";");
        $this->assertEquals($binaryString,$value);
        
        $all = '';
        for ($i = 0; $i <= 255; $i++) {
            $all .= chr($i);
        }
        $value = eval("return ".common_Utils::toPHPVariableString($all).";");
        $this->assertEquals($all,$value);
        
        $prophet = new Prophet();
        $e = $prophet->prophesize('generis_test_UtilsTest');

        $serialized = serialize($e->reveal());
        $value = eval("return ".common_Utils::toPHPVariableString($serialized).";");
        $this->assertEquals($serialized,$value);
	}
	
	public function testSerialisation(){
	    
	    $toSerialize = array(
	    	'a' => "te\0st \\ ",
	        'b' => new core_kernel_classes_Resource('doesnotexist'),
	        'c' => array(
	    	    '1', '2', array(common_user_auth_Service::singleton()) 
	        )
	    );
	    $value = eval("return ".common_Utils::toPHPVariableString($toSerialize).";");
        $this->assertEquals($toSerialize,$value);
	}
	
	private function buildBinString() {
	     
	    $position = pack('S', 0); // Q01
	    $state = "\x01"; // INTERACTING
	    $navigationMode = "\x00"; // LINEAR
	    $submissionMode = "\x00"; // INDIVIDUAL
	    $attempting = "\x00"; // false
	    $hasItemSessionControl = "\x00"; // false
	    $numAttempts = "\x02"; // 2
	    $duration = pack('S', 4) . 'PT0S'; // 0 seconds recorded yet.
	    $completionStatus = pack('S', 10) . 'incomplete';
	    $timeReference = pack('l', 1378302030); //  Wednesday, September 4th 2013, 13:40:30 (GMT)
	    $varCount = "\x02"; // 2 variables (SCORE & RESPONSE).
	     
	    $score = "\x01" . pack('S', 8) . "\x00" . "\x01" . pack('d', 1.0);
	    $response = "\x00" . pack('S', 0) . "\x00" . "\x01" . pack('S', 7) . 'ChoiceA';
	     
	    return implode('', array($position, $state, $navigationMode, $submissionMode, $attempting, $hasItemSessionControl, $numAttempts, $duration, $completionStatus, $timeReference, $varCount, $score, $response));
	}

}