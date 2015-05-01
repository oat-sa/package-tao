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
 *               2013-2014 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Conveniance function that calls tao_helpers_Display::htmlize
 *
 * @param  string $input The input string
 * @return string $output The htmlized string.
 */
function _dh($input){
	return tao_helpers_Display::htmlize($input);
}

/**
 * Convenience function clean the input string (replace all no alphanum chars).
 * 
 * @param  string $input The input string.
 * @return string $output The output string without non alphanum characters.
 */
function _clean($input){
	return tao_helpers_Display::textCleaner($input);
}

/**
 * Utility class focusing on display methods.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_helpers_Display
{

    /**
     * Enables you to cut a long string and end it with [...] and add an hover
     * to display the complete string on mouse over.
     *
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string input The string input.
     * @param  int maxLength (optional, default = 75) The maximum length for the result string.
     * @return string The cut string, enclosed in a <span> html tag. This tag received the 'cutted' CSS class.
     */
    public static function textCutter($input, $maxLength = 75)
    {
        $returnValue = (string) '';

		if (mb_strlen($input, TAO_DEFAULT_ENCODING) > $maxLength){
			$input = "<span title='$input' class='cutted' style='cursor:pointer;'>".mb_substr($input, 0, $maxLength, TAO_DEFAULT_ENCODING)."[...]</span>";
		}
		
		$returnValue = $input;

        return (string) $returnValue;
    }

    /**
     * Clean a text with a joker character to replace any characters that is
     * alphanumeric.
     *
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string input The string input.
     * @param  string joker (optional, default = '_') A joker character that will be the alphanumeric placeholder.
     * @param  int maxLength (optional, default = -1) output maximum length
     * @return string The result string.
     */
    public static function textCleaner($input, $joker = '_', $maxLength = -1)
    {
        $returnValue = (string) '';

        $randJoker = ($joker == '*');
        $length =  ((defined('TAO_DEFAULT_ENCODING')) ? mb_strlen($input, TAO_DEFAULT_ENCODING) : mb_strlen($input));
        if($maxLength > -1 ){
            $length = min($length, $maxLength);
        }

		$i = 0;
		while ($i < $length){
			if (preg_match("/^[a-zA-Z0-9_-]{1}$/u", $input[$i])){
				$returnValue .= $input[$i];
			}
			else{
				if ($input[$i] == ' '){
					$returnValue .= '_';
				}
				else{
					$returnValue .= ((true === $randJoker) ? chr(rand(97, 122)) : $joker);
				}
			}
			$i++;
		}

        return (string) $returnValue;
    }

    /**
     * Display clean and more secure text into an HTML page. The implementation
     * of this method is done with htmlentities.This method is Unicode safe.
     *
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string input The input string.
     * @return string The htmlized string.
     */
    public static function htmlize($input)
    {
        $returnValue = (string) '';

        $returnValue = htmlentities($input, ENT_COMPAT, TAO_DEFAULT_ENCODING);

        return (string) $returnValue;
    }
 /**
     *  Convert special characters to HTML entities
     */
    public static function htmlEscape($string) {
        return htmlspecialchars($string);
    }
    
    /**
     * Encode the value of an html attribute
     * 
     * @param string $string
     * @return string
     */
    public static function encodeAttrValue($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
