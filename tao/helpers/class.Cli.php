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


/**
 * Utility class focusing on the PHP CLI.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_helpers_Cli
{

    /**
     * A set of color codes that can be used to highlight texts in a CLI context.
     * Keys of these associative array are color names in english and values are color
     * codes. Available colors are:
     * 
     * $colors['background']['black']
     * $colors['background']['red']
     * $colors['background']['green']
     * $colors['background']['yellow']
     * $colors['background']['blue']
     * $colors['background']['magenta']
     * $colors['background']['cyan']
     * $colors['background']['light_gray']
     * $colors['foreground']['black']
     * $colors['foreground']['dark_gray']
     * $colors['foreground']['blue']
     * $colors['foreground']['light_blue']
     * $colors['foreground']['green']
     * $colors['foreground']['light_green']
     * $colors['foreground']['light_cyan']
     * $colors['foreground']['red']
     * $colors['foreground']['light_red']
     * $colors['foreground']['purple']
     * $colors['foreground']['brown']
     * $colors['foreground']['yellow']
     * $colors['foreground']['light_gray']
     * $colors['foreground']['white']
     *
     * @var array
     */
    private static $colors = array(
'background' => array(
	'black' 		=> '40',
	'red' 			=> '41',
	'green' 		=> '42',
	'yellow' 		=> '43',
	'blue' 			=> '44',
	'magenta' 		=> '45',
	'cyan' 			=> '46',
	'light_gray'	=> '47'
),
'foreground' => array(
	'black' 		=> '0;30',
	'dark_gray' 	=> '1;30',	
	'blue' 			=> '0;34',
	'light_blue'	=> '1;34',
	'green' 		=> '0;32',
	'light_green' 	=> '1;32',
	'cyan' 			=> '0;36',
	'light_cyan' 	=> '1;36',
	'red' 			=> '0;31',
	'light_red' 	=> '1;31',
	'purple' 		=> '0;35',
	'light_purple' 	=> '1;35',
	'brown' 		=> '0;33',
	'yellow' 		=> '1;33',
	'light_gray' 	=> '0;37',
	'white' 		=> '1;37'
));

    /**
     * Get a background color compliant with the CLI. Available color names are: 
     * black, red, green, yellow, blue, magenta, cyan, light_gray.
     * 
     * If the color name does not exist, an empty string is returned.
     *
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string name The name of the color.
     * @return string The corresponding color code.
     */
    public static function getBgColor($name)
    {
        $returnValue = (string) '';
        
        if(!empty($name) && array_key_exists($name, self::$colors['background'])){
        	$returnValue = self::$colors['background'][$name];
        }

        return (string) $returnValue;
    }

    /**
     * Get a foreground color compliant with the CLI. Available color names are:
     * black, dark_gray, blue, light_blue, green, light_green, light_cyan,
     * red, light_red, purple, brown, yellow, light_gray, white.
     * 
     * If the provided color names is not supported, an empty string is returned. Otherwise,
     * the corresponding color code is returned.
     *
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string name The color name.
     * @return string A color code.
     */
    public static function getFgColor($name)
    {
        $returnValue = (string) '';
        
    	if(!empty($name) && array_key_exists($name, self::$colors['foreground'])){
        	$returnValue = self::$colors['foreground'][$name];
        }

        return (string) $returnValue;
    }

}

?>