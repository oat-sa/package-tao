<?php
/**  
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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */
 
 /**
 * A utility class handling php language related tasks
 */
class helpers_PhpTools {
    
    /**
     * Returns an array that contains namespace and name of the class defined in the file
     * 
     * Code losely based on http://stackoverflow.com/questions/7153000/get-class-name-from-file
     * by user http://stackoverflow.com/users/492901/netcoder
     * 
     * @param string file to anaylse
     * @return array
     */
    static public function getClassInfo($file) {
	    $buffer = file_get_contents($file);
	    $tokens = @token_get_all($buffer);
	    $class = $namespace = $buffer = '';
        for ($i=0;$i<count($tokens);$i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                for ($j=$i+1;$j<count($tokens); $j++) {
                    if ($tokens[$j][0] === T_STRING) {
                        $namespace .= '\\'.$tokens[$j][1];
                    } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                        break;
                    }
                }
            }

            if ($tokens[$i][0] === T_CLASS) {
                for ($j=$i+1;$j<count($tokens);$j++) {
                    if ($tokens[$j] === '{') {
                        if (!isset($tokens[$i+2][1])) {
                            error_log($file.' does not contain a valid class definition');
                            break(2);
                        } else {
                            $class = $tokens[$i+2][1];
                            break(2);
                        }
                    }
                }
            }
        }
        return array(
        	'ns' => $namespace,
            'class' => $class
        );
    }
}