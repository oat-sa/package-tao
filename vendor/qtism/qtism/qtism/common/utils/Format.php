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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 *  
 *
 */
namespace qtism\common\utils;

use \DateInterval;
use \Exception;

/**
 * A utility class focusing on string format checks.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Format {
	
	private static $perlXmlIdeographic = '(?:\xE3\x80[\x87\xA1-\xA9]|\xE4(?:[\xB8-\xBF][\x80-\xBF])|\xE5(?:[\x80-\xBF][\x80-\xBF])|\xE6(?:[\x80-\xBF][\x80-\xBF])|\xE7(?:[\x80-\xBF][\x80-\xBF])|\xE8(?:[\x80-\xBF][\x80-\xBF])|\xE9(?:[\x80-\xBD][\x80-\xBF]|\xBE[\x80-\xA5]))';
	
	private static $perlXmlExtender = '(?:\xC2\xB7|\xCB[\x90\x91]|\xCE\x87|\xD9\x80|\xE0(?:\xB9\x86|\xBB\x86)|\xE3(?:\x80[\x85\xB1-\xB5]|\x82[\x9D\x9E]|\x83[\xBC-\xBE]))';
	
	private static $perlXmlBaseChar = '(?:[a-zA-Z]|\xC3[\x80-\x96\x98-\xB6\xB8-\xBF]|\xC4[\x80-\xB1\xB4-\xBE]|\xC5[\x81-\x88\x8A-\xBE]|\xC6[\x80-\xBF]|\xC7[\x80-\x83\x8D-\xB0\xB4\xB5\xBA-\xBF]|\xC8[\x80-\x97]|\xC9[\x90-\xBF]|\xCA[\x80-\xA8\xBB-\xBF]|\xCB[\x80\x81]|\xCE[\x86\x88-\x8A\x8C\x8E-\xA1\xA3-\xBF]|\xCF[\x80-\x8E\x90-\x96\x9A\x9C\x9E\xA0\xA2-\xB3]|\xD0[\x81-\x8C\x8E-\xBF]|\xD1[\x80-\x8F\x91-\x9C\x9E-\xBF]|\xD2[\x80\x81\x90-\xBF]|\xD3[\x80-\x84\x87\x88\x8B\x8C\x90-\xAB\xAE-\xB5\xB8\xB9]|\xD4[\xB1-\xBF]|\xD5[\x80-\x96\x99\xA1-\xBF]|\xD6[\x80-\x86]|\xD7[\x90-\xAA\xB0-\xB2]|\xD8[\xA1-\xBA]|\xD9[\x81-\x8A\xB1-\xBF]|\xDA[\x80-\xB7\xBA-\xBE]|\xDB[\x80-\x8E\x90-\x93\x95\xA5\xA6]|\xE0(?:\xA4[\x85-\xB9\xBD]|\xA5[\x98-\xA1]|\xA6[\x85-\x8C\x8F\x90\x93-\xA8\xAA-\xB0\xB2\xB6-\xB9]|\xA7[\x9C\x9D\x9F-\xA1\xB0\xB1]|\xA8[\x85-\x8A\x8F\x90\x93-\xA8\xAA-\xB0\xB2\xB3\xB5\xB6\xB8\xB9]|\xA9[\x99-\x9C\x9E\xB2-\xB4]|\xAA[\x85-\x8B\x8D\x8F-\x91\x93-\xA8\xAA-\xB0\xB2\xB3\xB5-\xB9\xBD]|\xAB\xA0|\xAC[\x85-\x8C\x8F\x90\x93-\xA8\xAA-\xB0\xB2\xB3\xB6-\xB9\xBD]|\xAD[\x9C\x9D\x9F-\xA1]|\xAE[\x85-\x8A\x8E-\x90\x92-\x95\x99\x9A\x9C\x9E\x9F\xA3\xA4\xA8-\xAA\xAE-\xB5\xB7-\xB9]|\xB0[\x85-\x8C\x8E-\x90\x92-\xA8\xAA-\xB3\xB5-\xB9]|\xB1[\xA0\xA1]|\xB2[\x85-\x8C\x8E-\x90\x92-\xA8\xAA-\xB3\xB5-\xB9]|\xB3[\x9E\xA0\xA1]|\xB4[\x85-\x8C\x8E-\x90\x92-\xA8\xAA-\xB9]|\xB5[\xA0\xA1]|\xB8[\x81-\xAE\xB0\xB2\xB3]|\xB9[\x80-\x85]|\xBA[\x81\x82\x84\x87\x88\x8A\x8D\x94-\x97\x99-\x9F\xA1-\xA3\xA5\xA7\xAA\xAB\xAD\xAE\xB0\xB2\xB3\xBD]|\xBB[\x80-\x84]|\xBD[\x80-\x87\x89-\xA9])|\xE1(?:\x82[\xA0-\xBF]|\x83[\x80-\x85\x90-\xB6]|\x84[\x80\x82\x83\x85-\x87\x89\x8B\x8C\x8E-\x92\xBC\xBE]|\x85[\x80\x8C\x8E\x90\x94\x95\x99\x9F-\xA1\xA3\xA5\xA7\xA9\xAD\xAE\xB2\xB3\xB5]|\x86[\x9E\xA8\xAB\xAE\xAF\xB7\xB8\xBA\xBC-\xBF]|\x87[\x80-\x82\xAB\xB0\xB9]|[\xB8\xB9][\x80-\xBF]|\xBA[\x80-\x9B\xA0-\xBF]|\xBB[\x80-\xB9]|\xBC[\x80-\x95\x98-\x9D\xA0-\xBF]|\xBD[\x80-\x85\x88-\x8D\x90-\x97\x99\x9B\x9D\x9F-\xBD]|\xBE[\x80-\xB4\xB6-\xBC\xBE]|\xBF[\x82-\x84\x86-\x8C\x90-\x93\x96-\x9B\xA0-\xAC\xB2-\xB4\xB6-\xBC])|\xE2(?:\x84[\xA6\xAA\xAB\xAE]|\x86[\x80-\x82])|\xE3(?:\x81[\x81-\xBF]|\x82[\x80-\x94\xA1-\xBF]|\x83[\x80-\xBA]|\x84[\x85-\xAC])|\xEA(?:[\xB0-\xBF][\x80-\xBF])|\xEB(?:[\x80-\xBF][\x80-\xBF])|\xEC(?:[\x80-\xBF][\x80-\xBF])|\xED(?:[\x80-\x9D][\x80-\xBF]|\x9E[\x80-\xA3]))';
	
	private static $perlXmlDigit = '(?:[0-9]|\xD9[\xA0-\xA9]|\xDB[\xB0-\xB9]|\xE0(?:\xA5[\xA6-\xAF]|\xA7[\xA6-\xAF]|\xA9[\xA6-\xAF]|\xAB[\xA6-\xAF]|\xAD[\xA6-\xAF]|\xAF[\xA7-\xAF]|\xB1[\xA6-\xAF]|\xB3[\xA6-\xAF]|\xB5[\xA6-\xAF]|\xB9[\x90-\x99]|\xBB[\x90-\x99]|\xBC[\xA0-\xA9]))';
	
	private static $perlXmlCombiningChar = '(?:\xCC[\x80-\xBF]|\xCD[\x80-\x85\xA0\xA1]|\xD2[\x83-\x86]|\xD6[\x91-\xA1\xA3-\xB9\xBB-\xBD\xBF]|\xD7[\x81\x82\x84]|\xD9[\x8B-\x92\xB0]|\xDB[\x96-\xA4\xA7\xA8\xAA-\xAD]|\xE0(?:\xA4[\x81-\x83\xBC\xBE\xBF]|\xA5[\x80-\x8D\x91-\x94\xA2\xA3]|\xA6[\x81-\x83\xBC\xBE\xBF]|\xA7[\x80-\x84\x87\x88\x8B-\x8D\x97\xA2\xA3]|\xA8[\x82\xBC\xBE\xBF]|\xA9[\x80-\x82\x87\x88\x8B-\x8D\xB0\xB1]|\xAA[\x81-\x83\xBC\xBE\xBF]|\xAB[\x80-\x85\x87-\x89\x8B-\x8D]|\xAC[\x81-\x83\xBC\xBE\xBF]|\xAD[\x80-\x83\x87\x88\x8B-\x8D\x96\x97]|\xAE[\x82\x83\xBE\xBF]|\xAF[\x80-\x82\x86-\x88\x8A-\x8D\x97]|\xB0[\x81-\x83\xBE\xBF]|\xB1[\x80-\x84\x86-\x88\x8A-\x8D\x95\x96]|\xB2[\x82\x83\xBE\xBF]|\xB3[\x80-\x84\x86-\x88\x8A-\x8D\x95\x96]|\xB4[\x82\x83\xBE\xBF]|\xB5[\x80-\x83\x86-\x88\x8A-\x8D\x97]|\xB8[\xB1\xB4-\xBA]|\xB9[\x87-\x8E]|\xBA[\xB1\xB4-\xB9\xBB\xBC]|\xBB[\x88-\x8D]|\xBC[\x98\x99\xB5\xB7\xB9\xBE\xBF]|\xBD[\xB1-\xBF]|\xBE[\x80-\x84\x86-\x8B\x90-\x95\x97\x99-\xAD\xB1-\xB7\xB9])|\xE2\x83[\x90-\x9C\xA1]|\xE3(?:\x80[\xAA-\xAF]|\x82[\x99\x9A]))';
	
	private static $printfFormatSpecifier = '%(?:(?:-|\+| |#|0)*){0,1}(?:[0-9]+|\*){0,1}(?:\.[0-9]+|\*){0,1}(?:hh|h|ll|l|j|z|t|L){0,1}(?:d|i|u|o|x|X|f|F|e|E|g|G|a|A|c|s|p|n)';
	
	/**
	 * Check if string is compliant with the identifier datatype of IMS QTI. 
	 * 
	 * IMS Global says :
	 * Identifiers can contain the character classes Letter, Digit, Combining which are described in the
	 * Extensible Markup Language (XML) 1.0 (Second Edition). Identifiers should have no more 
	 * than 32 characters for compatibility with version 1. They are always compared case-sensitively.
	 * 
	 * @link http://www.w3.org/TR/2000/REC-xml-20001006
	 * @return boolean Wether $string is a valid identifier.
	 */
	static public function isIdentifier($string, $strict = true) {
		if ($strict === true) {
			$letter = self::getPerlXmlLetter();
			$digit = self::getPerlXmlDigit();
			$combiningChar = self::getPerlXmlCombiningChar();
			$extender = self::getPerlXmlExtender();
			
			$string = str_split($string);
			$first = array_shift($string);
			
			if (preg_match("/(?:_|${letter})/u", $first) === 1) {
				foreach ($string as $s) {
					if (preg_match("/${letter}|${digit}/u", $s) === 0 && preg_match("/${combiningChar}|${extender}/u", $s) === 0 && preg_match("/_|\\-|\\./u", $s) === 0) {
						return false;
					}
				}
				
				return true;
			}
			else {
				return false;
			}	
		}
		else {
			return preg_match("/^[a-zA-Z_][a-zA-Z0-9_\.-]*$/u", $string) === 1;
		}
	}
	
	/**
	 * Get Perl XML Digit regular expression.
	 * 
	 * @return string A perl regular expression.
	 * @link http://cpansearch.perl.org/src/TJMATHER/XML-RegExp-0.04/lib/XML/RegExp.pm
	 */
	static private function getPerlXmlDigit() {
		return self::$perlXmlDigit;
	}
	
	/**
	 * Get Perl XML BaseChar regular expression.
	 * 
	 * @return string A perl regular expression.
	 * @link http://cpansearch.perl.org/src/TJMATHER/XML-RegExp-0.04/lib/XML/RegExp.pm
	 */
	static private function getPerlXmlBaseChar() {
		return self::$perlXmlBaseChar;
	}
	
	/**
	 * Get Perl XML Ideographic regular expression.
	 * 
	 * @return string A perl regular expression.
	 * @link http://cpansearch.perl.org/src/TJMATHER/XML-RegExp-0.04/lib/XML/RegExp.pm
	 */
	static private function getPerlXmlIdeographic() {
		return self::$perlXmlIdeographic;
	}
	
	/**
	 * Get Perl XML Letter regular expression.
	 * 
	 * @return string A perl regular expression.
	 * @link http://cpansearch.perl.org/src/TJMATHER/XML-RegExp-0.04/lib/XML/RegExp.pm
	 */
	static private function getPerlXmlLetter() {
		$baseChar = self::getPerlXmlBaseChar();
		$ideographic = self::getPerlXmlIdeographic();
		return "(?:${baseChar}|${ideographic})";
	}
	
	/**
	 * Get Perl XML CombiningChar regular expression.
	 * 
	 * @return string A perl regular expression.
	 * @link http://cpansearch.perl.org/src/TJMATHER/XML-RegExp-0.04/lib/XML/RegExp.pm
	 */
	static private function getPerlXmlCombiningChar() {
		return self::$perlXmlCombiningChar;
	}
	
	/**
	 * Get Perl XML Extender regular expression.
	 * 
	 * @return string A perl regular expression.
	 * @link http://cpansearch.perl.org/src/TJMATHER/XML-RegExp-0.04/lib/XML/RegExp.pm
	 */
	static private function getPerlXmlExtender() {
		return self::$perlXmlExtender;
	}
	
	/**
	 * Get Perl printf format specifier regular expression.
	 * 
	 * @return string A perl regular expression.
	 * @see http://www.cplusplus.com/reference/cstdio/printf/ C++ printf format.
	 */
	static private function getPrintfFormatSpecifier() {
	    return self::$printfFormatSpecifier;
	}
	
	/**
	 * Apply both PHP::strtolower and PHP::trim on a given $string.
	 *
	 * @param string $string A string on which you want to apply PHP::strtolower and PHP::trim.
	 * @return string An altered string.
	 */
	static public function toLowerTrim($string) {
		return strtolower(trim($string));
	}
	
	/**
	 * Wether a given $string is a URI.
	 *
	 * @param string $string A string value.
	 * @return boolean Wether $string is a valid URI.
	 * @link http://en.wikipedia.org/wiki/Uniform_Resource_Identifier
	 */
	static public function isUri($string) {
		// @todo find the ultimate URI validation rule.
		return gettype($string) === 'string';
		
		// Thanks to Wizard04.
		$pattern = "<^([a-z0-9+.-]+):(?://(?:((?:[a-z0-9-._~!$&'\(\)*+,;=:]|%[0-9A-F]{2})*)@)?((?:[a-z0-9-._~!$&'()*+,;=]|%[0-9A-F]{2})*)(?::(\d*))?(/(?:[a-z0-9-._~!$&'()*+,;=:@/]|%[0-9A-F]{2})*)?|(/?(?:[a-z0-9-._~!$&'()*+,;=:@]|%[0-9A-F]{2})+(?:[a-z0-9-._~!$&'()*+,;=:@/]|%[0-9A-F]{2})*)?)(?:\?((?:[a-z0-9-._~!$&'()*+,;=:/?@]|%[0-9A-F]{2})*))?(?:#((?:[a-z0-9-._~!$&'()*+,;=:/?@]|%[0-9A-F]{2})*))?$>i";
		return (preg_match($pattern, $string) === 1);
	}
	
	/**
	 * Wether a given $string can be cast into an integer value.
	 * 
	 * @param string $string A string value.
	 * @return boolean Wether $string can be cast into an integer value.
	 */
	static public function isInteger($string) {
		return (preg_match('/^(?:\\-|\\+){0,1}[0-9]+$/', self::toLowerTrim($string)) === 1);
	}
	
	/**
	 * Wether a given $string can be cast into a float value.
	 * 
	 * @param string $string A string value e.g. '27.111'.
	 * @return boolean Wether $string can be converted to a float.
	 */
	static public function isFloat($string) {
		return (preg_match('/^(?:(?:\\-|\\+){0,1}[0-9]+)$|^(?:(?:\\-|\\+){0,1}[0-9]+\\.[0-9]+)+/', Format::toLowerTrim($string)) === 1);
	}
	
	/**
	 * Wether a given $string can be cast into a pair.
	 * 
	 * @param string $string A string value.
	 * @return boolean Wether $string can be converted to a pair.
	 */
	static public function isPair($string) {
		$pair = explode("\x20", $string);
		
		if (count($pair) == 2) {
			if (self::isIdentifier(self::toLowerTrim($pair[0])) && self::isIdentifier(self::toLowerTrim($pair[1]))) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Wether a given $string can be cast into a directed pair.
	 *
	 * @param string $string A string value.
	 * @return boolean Wether $string can be converted to a directed pair.
	 */
	static public function isDirectedPair($string) {
		return self::isPair($string);
	}
	
	/**
	 * Wether a given $string can be cast into a duration.
	 * 
	 * @param string $string A string value.
	 * @return boolean Wether $string can be converted to a duration.
	 */
	static public function isDuration($string) {
		try {
			$duration = new DateInterval($string);
			return true;
		}
		catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * Whether a given $string can be transformed into a boolean.
	 * 
	 * @param string $string A string value.
	 * @return boolean Whether $string can be converted to a boolean.
	 */
	static public function isBoolean($string) {
		if (gettype($string) === 'string') {
			$string = self::toLowerTrim($string);
			if ($string == 'true') {
				return true;
			}
			else if ($string == 'false') {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	
	/**
	 * Whether a given $string can be cast into a Point datatype.
	 * 
	 * @param string $string A string value.
	 * @return boolean Wheter $string can be transformed to a Point datatype.
	 */
	static public function isPoint($string) {
		if (gettype($string) === 'string') {
			$parts = explode("\x20", $string);
			if (count($parts) == 2) {
				if (self::isInteger($parts[0]) && self::isInteger($parts[1])) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Whether a given $string can be cast into the file baseType.
	 * 
	 * @param string $string A string value.
	 * @return boolean
	 */
	static public function isFile($string) {
		// @todo implement File baseType as a complex type. See QTI-PCI spec for redemption.
		return gettype($string) === 'string';
	}
	
	/**
	 * Whether or not a given string is a variable ref.
	 * 
	 * @param string $string A given string.
	 * @return boolean Wheter $string is a valid variable ref.
	 * @example '{myIdentifier1}' is a valid variable ref but 'myIdentifier1' is not.
	 */
	static public function isVariableRef($string) {
		$firstBrace = substr($string, 0, 1);
		$secondBrace = substr($string, -1);
		
		if ($firstBrace == '{' && $secondBrace == '}') {
			return self::isIdentifier(substr($string, 1, -1));
		}
		else {
			return self::isIdentifier($string);
		}
	}
	
	/**
	 * Whether or not a given string is a coordinate collection.
	 * 
	 * For compatibility reasons, float formatted numbers will also
	 * be accepted as valid numbers to compose coordinates.
	 * 
	 * @param string $string A given string.
	 * @return boolean Wether $string is a valid coordinate collection.
	 * @example '0, 20, 100, 20' is a valid coordinate collection to describe a rectangle shape.
	 */
	static public function isCoords($string) {
		$pattern = "/^-{0,1}[0-9]+(?:\.[0-9]+){0,1}(?:\s*,\s*-{0,1}[0-9]+(?:\.[0-9]+){0,1})*$/";
		return preg_match($pattern, $string) === 1;
	}
	
	/**
	 * Whether a given $string is a string value with a maximum
	 * of 256 characters.
	 * 
	 * @param string $string A string value.
	 * @return boolean
	 */
	static public function isString256($string) {
	    return is_string($string) && mb_strlen($string, 'UTF-8') <= 256;
	}
	
	/**
	 * Whether a given $string is a valid value for a body element's class
	 * attribute e.g. 'qti-label' or 'qti-label qti-component'.
	 * 
	 * @param string $string A string value.
	 * @return boolean
	 */
	static public function isClass($string) {
	    $pattern = "/^(?:[^\s]+?(?:\x20){0,1})+$/";
	    return preg_match($pattern, $string) === 1;
	}
	
	/**
	 * Get a float variable into a Standard Form / Scientific notation e.g. 6.72 x 10⁴.
	 * 
	 * * If no $precision is given, 6 significant numbers will be displayed after the decimal separator.
	 * * If no $x is given, the 'x' character will be used as the 'times' operator.
	 * 
	 * @param float $float
	 * @param string $x The character to be used as the 'times' operator.
	 * @param false|integer $precision The number of requested significant numbers after the decimal separator.
	 * @return string
	 */
	static public function scale10($float, $x = 'x', $precision = false) {

	    // 1. Transform in 'E' notation.
	    $mask = ($precision === false) ? '%e' : "%.${precision}e";
	    $strFloat = sprintf($mask, $float);
	    
	    // 2. Transform the 'E' notation into 'x 10^n' notation.
	    $parts = explode('e', $strFloat);
	    $mantissa = $parts[1];
	    $newMantissa = '';
	    
	    for ($i = 0; $i < strlen($mantissa); $i++) {
	        switch ($mantissa[$i]) {
	            case '0':
	                $newMantissa .= json_decode('"\u2070"');
	            break;
	            
	            case '1':
	                $newMantissa .= json_decode('"\u00b9"');
	            break;
	            
	            case '2':
	                $newMantissa .= json_decode('"\u00b2"');
	            break;
	            
	            case '3':
	                $newMantissa .= json_decode('"\u00b3"');
	            break;
	            
	            case '4':
	                $newMantissa .= json_decode('"\u2074"');
	            break;
	            
	            case '5':
	                $newMantissa .= json_decode('"\u2075"');
	            break;
	            
	            case '6':
	                $newMantissa .= json_decode('"\u2076"');
	            break;
	            
	            case '7':
	                $newMantissa .= json_decode('"\u2077"');
	            break;
	            
	            case '8':
	                $newMantissa .= json_decode('"\u2078"');
	            break;
	            
	            case '9':
	                $newMantissa .= json_decode('"\u2079"');
	            break;
	            
	            case '-':
	                $newMantissa .= json_decode('"\u207b"');
	            break;
	        }
	    }
	    
	    return $parts[0] . " ${x} 10" . $newMantissa;
	}
	
	/**
	 * This method is not absolutely safe because of the 'volatile' nature of 
	 * the printf format. However, it can validate a lot of strings as a first
	 * barrier for further code to be implemented.
	 * 
	 * Examples:
	 * 
	 * Format::isPrintfIsoFormat('%#x') => true
	 * Format::isPrintfIsoFormat('%#llx') => true
	 * Format::isPrintfIsoFormat('Octal %#x is Octal %#llx') => true
	 * Format::isPrintfIsoFormat("%d\n") => true
	 * Format::isPrintfIsoFormat("%3d\n") => true
	 * Format::isPrintfIsoFormat("%03d\n") => true
	 * Format::isPrintfIsoFormat("Characters: %c %c \n") => true
	 * Format::isPrintfIsoFormat("Decimals: %d %ld\n") => true
	 * Format::isPrintfIsoFormat("Preceding with blanks: %10d \n") => true
	 * Format::isPrintfIsoFormat("Preceding with zeros: %010d \n") => true
	 * Format::isPrintfIsoFormat("Some different radices: %d %x %o %#x %#o \n") => true
	 * Format::isPrintfIsoFormat("floats: %4.2f %+.0e %E \n") => true
	 * Format::isPrintfIsoFormat("Width trick: %*d \n") => true
	 * Format::isPrintfIsoFormat("%s \n") => true
	 * Format::isPrintfIsoFormat("%3d %06.3f\n") => true
	 * Format::isPrintfIsoFormat("The color: %s\n") => true
	 * Format::isPrintfIsoFormat("First number: %d\n") => true
	 * Format::isPrintfIsoFormat("Second number: %04d\n") => true
	 * Format::isPrintfIsoFormat("Third number: %i\n") => true
	 * Format::isPrintfIsoFormat("Float number: %3.2f\n") => true
	 * Format::isPrintfIsoFormat("Hexadecimal: %x\n") => true
	 * Format::isPrintfIsoFormat("Octal: %o\n") => true
	 * Format::isPrintfIsoFormat("Unsigned value: %u\n") => true
	 * Format::isPrintfIsoFormat("Just print the percentage sign %%\n") => false // Do not contain valid specifier.
	 * Format::isPrintfIsoFormat(":%s:\n") => true
	 * Format::isPrintfIsoFormat(":%15s:\n") => true
	 * Format::isPrintfIsoFormat(":%.10s:\n") => true
	 * Format::isPrintfIsoFormat(":%-10s:\n") => true
	 * Format::isPrintfIsoFormat(":%-15s:\n") => true
	 * Format::isPrintfIsoFormat(":%.15s:\n") => true
	 * Format::isPrintfIsoFormat(":%15.10s:\n") => true
	 * Format::isPrintfIsoFormat(":%-15.10s:\n") => true
	 * Format::isPrintfIsoFormat("This is an integer with padding %03d\n") => true
	 * Format::isPrintfIsoFormat('This is an integer with padding...') => false
	 * Format::isPrintfIsoFormat("Escape or not? %%s") => false
	 * Format::isPrintfIsoFormat("Escape or not? %%%s") => true
	 * Format::isPrintfIsoFormat("Escape or not? %%%%s") => false
	 * Format::isPrintfIsoFormat("Escape or not? %%%%%s") => true
	 * Format::isPrintfIsoFormat("%s bla %s and %%%s is %s and %%%%s") => true
	 * Format::isPrintfIsoFormat("%%s bla %s and %%%s is %s and %%%%s") => true
	 * Format::isPrintfIsoFormat("%%s bla %%s and %%%s is %s and %%%%s") => true
	 * Format::isPrintfIsoFormat("%%s bla %%s and %%s is %s and %%%%s") => true
	 * Format::isPrintfIsoFormat("%%s bla %%s and %%s is %%%%s and %%%%s") => false
	 * Format::isPrintfIsoFormat("%s") => true
	 * Format::isPrintfIsoFormat("%S") => false
	 * Format::isPrintfIsoFormat("bla %S bli %s") => true
	 * 
	 * @param string $isoFormat
	 * @return boolean 
	 */
	static public function isPrintfIsoFormat($isoFormat) {
	    $subPattern = self::getPrintfFormatSpecifier();
	    $pattern = '/(?:(?:[^%]|^)(?:%%)+(' . $subPattern . '))|(?:(?:[^%])(' . $subPattern . '))|(?:^(' . $subPattern . '))/u';
	    
	    $matches = array();
	    preg_match_all($pattern, $isoFormat, $matches);
	    
	    if (count($matches[1]) + count($matches[2]) + count($matches[3]) > 0) {
	        // There is at least one format specifier in the string.
	        return true;
	    }
	    else {
	        return false;
	    }
	}
	
	/**
	 * Transform an ISO number formatting into a format that can
	 * be handled by PHP's printf/sprintf implementation.
	 * 
	 * Please note that this method is not totally safe because
	 * of the "funky" nature of ISO number formats especially when
	 * used with C++. However, this method can be a good first attempt
	 * of transformation for further instructions to be executed.
	 * 
	 * @param string $isoFormat
	 * @return string The transformed formatting.
	 */
	static public function printfFormatIsoToPhp($isoFormat) {
	    
	    // Valid format, do the modifications to be compliant with
	    // PHP's printf.
	    $pattern = '/' . self::getPrintfFormatSpecifier() . '/u';
	    $matches = array();
	    preg_match_all($pattern, $isoFormat, $matches);
	    
	    foreach ($matches[0] as $m) {
	        // Don't worry, str_replace is multibyte safe!
	        $newM = str_replace('#', '', $m);
	        $newM = str_replace(array('h', 'l', 'j', 'z', 't', 'L'), '', $newM);
	        $newM = str_replace(array('i', 'a', 'A', 'c', 'p', 'n', 'O'), array('d', 'x', 'X', 's', 'x', 'd', 'o'), $newM);
	        $isoFormat = str_replace($m, $newM, $isoFormat);
	    }
	    
	    return $isoFormat;
	}
	
	/**
	 * Whether or not a given $length is a compliant XHTML
	 * length (e.g. "10%", 10, ...).
	 * 
	 * @param mixed $length A length as a string or integer.
	 * @return boolean
	 */
	static public function isXhtmlLength($length) {
	    if (is_int($length) === true) {
	        return $length >= 0;
	    }
	    else if (is_string($length) === true) {
	        return preg_match("/[0-9]+%/", $length) === 1;
	    }
	    else {
	        return false;
	    }
	}
}