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
namespace qtism\runtime\expressions\operators;

use qtism\common\utils\Format;

/**
 * A utility class for all sub-classes of the OperatorProcessor class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Utils {
	
	/**
	 * Compute the GCD (Greatest Common Divider) of $a and $b.
	 * 
	 * If either $a or $b is negative, its absolute value will be used
	 * instead.
	 * 
	 * @param integer $a A positive integer
	 * @param integer $b A positive integer
	 * @return integer The GCD of $a and $b.
	 */
	static public function gcd($a, $b) {
		$a = abs($a);
		$b = abs($b);
		
		$k = max($a, $b);
		$m = min($a, $b);
		
		while ($m !== 0) {
			$r = $k % $m;
			$k = $m;
			$m = $r;
		}
		
		return $k;
	}
	
	/**
	 * Compute LCM (Least Common Multiple) of $a and $b.
	 * 
	 * @param integer $a
	 * @param integer $b
	 * @return integer the LCM of $a and $b.
	 */
	static public function lcm($a, $b) {
		$a = abs($a);
		$b = abs($b);
		
		if ($a === 0 || $b === 0) {
			return 0;
		}
		
		$a = $a / self::gcd($a, $b);
		return $a * $b; 
	}
	
	/**
	 * Compute the arithmetic mean of $sample.
	 * 
	 * @param array An array of numeric values.
	 * @return false|number The arithmetic mean of $sample or false if any of the values of $sample is not numeric or if $sample is empty.
	 */
	static public function mean(array $sample) {
		
		$count = count($sample);
		if ($count === 0) {
			return false;
		}
		
		$sum = 0;
		foreach ($sample as $s) {
			$sType = gettype($s);
			
			if ($sType !== 'integer' && $sType !== 'double') {
				// only deal with numeric values.
				return false;
			}
			
			$sum += $s;
		}
		
		return $sum / $count;
	}
	
	/**
	 * Compute the variance of $sample.
	 * 
	 * * To compute the population variance: $sample is considered as a population if $correction equals false.
	 * * To compute the sample variance: $sample is considered as sample if $correction equals true.
	 * 
	 * IMPORTANT: 
	 * If $correction is true, $sample must contain more than 1 value, otherwise this method 
	 * returns false.
	 * 
	 * @param array $sample An array of numeric values.
	 * @param boolean $correction (optional) Apply the Bessel's correction on the computed variance.
	 * @return false|number The variance of $sample or false if $sample is empty or contains non-numeric values.
	 * @link http://en.wikipedia.org/wiki/Variance#Population_variance_and_sample_variance
	 */
	static public function variance(array $sample, $correction = true) {
		$mean = static::mean($sample);
		
		if ($mean === false) {
			return false;
		}
		
		// We are sure that
		// 1. $sample is not empty.
		// 2. $sample contains only numeric values.
		$count = count($sample);
		if ($correction === true && $count <= 1) {
			return false;
		}
		
		// because self::mean returns false if $sample is empty
		// or if it contains non-numeric values, we do not have to
		// check that fact anymore.
 		$sum = 0;
		
    	foreach ($sample as $s) {
        	$sum += pow($s - $mean, 2);
    	}
 
    	$d = ($correction === true) ? $count - 1 : $count;
    	
    	return $sum / $d;
	}
	
	/**
	 * Compute the standard deviation of $sample.
	 * 
	 * * To compute the population standard deviation: $sample is considered as a population if $correction equals false.
	 * * To compute the sample standard deviation: $sample is considered as sample if $correction equals true.
	 * 
	 * IMPORTANT: 
	 * If $correction is true, $sample must contain more than 1 value, otherwise this method 
	 * returns false.
	 * 
	 * @param array $sample An array of numeric values.
	 * @param boolean $correction (optional) Whether to apply Bessel's correction.
	 * @return false|number The standard deviation of $sample or false if $sample is empty or contains non-numeric values.
	 * @link http://en.wikipedia.org/wiki/Variance#Population_variance_and_sample_variance 
	 */
	static public function standardDeviation(array $sample, $correction = true) {
		$sampleVariance = static::variance($sample, $correction);
		
		if ($sampleVariance === false) {
			// non numeric values found in $sample or empty $sample or $correction applied
			// but count($sample) <= 1.
			return false;
		}
		
		return sqrt($sampleVariance);
	}
	
	/**
	 * Add an appropriate delimiter (/) to a regular expression that has no delimiters. This
	 * method is multi-byte safe safe.
	 *
	 * @return string|boolean The delimited string or false if no appropriate delimiters can be found.
	 */
	static public function pregAddDelimiter($string) {
		
		return '/' . static::escapeSymbols($string, '/') . '/';
	}
	
	/**
	 * Get the amout of backslash (\) characters in $string that precede $offset.
	 *
	 * @param string $string
	 * @param integer $offset
	 * @return integer
	 */
	static public function getPrecedingBackslashesCount($string, $offset) {
		$count = 0;
	
		if ($offset < strlen($string)) {
			for ($i = $offset; $i > 0; $i--) {
				if ($string[$i - 1] === '\\') {
					$count++;
				}
				else {
					break;
				}
			}
		}
	
		return $count;
	}
	
	/**
	 * Escape with a backslash (\) the $symbols in $string.
	 * 
	 * @param string $string
	 * @param array|string $symbols An array of symbols or a single symbol.
	 * @return string The escaped string.
	 */
	static public function escapeSymbols($string, $symbols) {
		
		if (!is_array($symbols)) {
			$symbols = array($symbols);
		}
		
		$len = mb_strlen($string, 'UTF-8');
		$returnValue = '';
		
		for ($i = 0; $i < $len; $i++) {
			$char = mb_substr($string, $i, 1); // get a multi-byte char.
			if (in_array($char, $symbols) === true) {
				
				// Check escaping.
				// If the amount of preceding backslashes is odd, it is escaped.
				// If the amount of preceding backslashes is even, it is not escaped.
				if (static::getPrecedingBackslashesCount($string, $i) % 2 === 0) {
					// It is not escaped, so ecape it.
					$returnValue .= '\\';
				}
			}
		
			$returnValue .= $char;
		}
		
		return $returnValue;
	}
	
	/**
	 * Transform a custom operator class e.g. 'org.qtism.custom.explode' into a PHP
	 * fully qualified class name e.g. 'org\qtism\custom\Explode'.
	 * 
	 * @param string $class A custom operator class name where namespace separator is '.' (dot).
	 * @return boolean|string A fully qualified PHP class name corresponding to $class or false if the transformation failed.
	 */
	static public function customOperatorClassToPhpClass($class) {
	    
	    if (is_string($class) === false) {
	        return false;
	    }
	    else if (Format::isIdentifier($class, false) === false) {
	        return false;
	    }
	    
	    $class = strval($class);
	    $tokens = explode('.', $class);
	    
	    if ($tokens === false) {
	        return $tokens;
	    }
	    else {
	        $tokenCount = count($tokens);
	        
	        if ($tokenCount <= 1) {
	            return false;
	        }
	        
	        // ucfirst on last token (i.e. The actual class name)
	        $lastPosition = $tokenCount - 1;
	        $lastToken = ucfirst($tokens[$lastPosition]);
	        $tokens[$lastPosition] = $lastToken;
	        
	        return implode("\\", $tokens);
	    }
	}
}