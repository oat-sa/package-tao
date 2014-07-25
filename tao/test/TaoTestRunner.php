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

require_once dirname(__FILE__).'/../includes/class.Bootstrap.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';
require_once INCLUDES_PATH.'/ClearFw/core/simpletestRunner/_main.php';
require_once dirname(__FILE__) .'/coverage/coverage.conf.php';

/**
 * Help you to run the test into the TAO Context
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage test
 */
class TaoTestRunner extends TestRunner{
	
	const SESSION_KEY = 'TAO_TEST_SESSION';
    /**
     *
     * @var boolean
     */
    private static $connected = false;


    /**
     * shared methods for test initialization
     */
    public static function initTest(){
        //connect the API
        if(!self::$connected){
            common_session_SessionManager::startSession(new common_test_TestUserSession());
            self::$connected = true;
        }
    }

	/**
	 * get the list of unit tests
	 * @param null|array $extensions if null all extension, else the list of extensions to look for the tests
	 * @param boolean $recursive if true it checks the subfoldfer
	 * @return array he list of test cases paths
	 */
	public static function getTests($extensions = null, $recursive = false){
		
		$tests = array();
		foreach(scandir(ROOT_PATH) as $extension){
			if(!preg_match("/^\./", $extension)){
				$getTests = false;
				if(is_null($extensions)){
					$getTests = true;
				}
				elseif(is_array($extensions)){
					if(in_array($extension, $extensions)){
						$getTests = true;
					}
				}
				if($getTests){
					$extTestPath = ROOT_PATH . '/' . $extension . '/test';
					$tests = array_merge($tests, self::findTest($extTestPath, $recursive));
				}
			}
		}
		return $tests;
	}
	

}
?>
