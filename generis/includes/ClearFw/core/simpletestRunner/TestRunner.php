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

set_time_limit(900);	//a suite must never takes more than 15minutes!

/**
 *
 * Help you to run the test into the ClearFw Context
 *
 * @author CRP Henri Tudor - TAO Team
 * @license GPLv2
 *
 */

class TestRunner
{
    /**
     * Search and find test case into a directory
     * @param string $path to folder to search in
     * @param boolean $recursive if true it checks the subfoldfer
     * @return array the list of test cases paths
     */
    public static function findTest($path, $recursive = false){
        $tests = array();
        if(file_exists($path)){
            if(is_dir($path)){
                foreach(scandir($path) as $file){
                    if(!preg_match("/^\./",$file)){
                        if(is_dir($path."/".$file) && $recursive){
                            $tests = array_merge($tests, self::findTest($path."/".$file, true));
                        }
                        if(preg_match("/TestCase\.php$/", $file)){
                            $tests[] = $path."/".$file;
                        }
                    }
                }
            }
        }
        return $tests;
    }

}
