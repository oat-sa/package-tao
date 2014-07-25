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
namespace qtism\runtime\common;

use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\IntOrIdentifier;
use qtism\common\datatypes\Uri;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Boolean;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\QtiDatatype;
use qtism\common\enums\Cardinality;
use qtism\common\datatypes\Duration;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\DirectedPair;
use qtism\common\datatypes\Point;
use qtism\common\enums\BaseType;
use qtism\common\utils\Format;
use qtism\runtime\common\Utils as RuntimeUtils;
use \InvalidArgumentException;
use \RuntimeException;

// @todo write an isNull method that applies on both scalar and container values.

class Utils {
	
	/**
	 * Whether a given primitive $value is compliant with the QTI runtime model.
	 *
	 * @param mixed $value A value you want to check the compatibility with the QTI runtime model.
	 * @return boolean
	 */
	public static function isRuntimeCompliant($value) {
		if ($value === null) {
		    return true;
		}
		else if ($value instanceof QtiDatatype) {
		    return true;
		}
		else {
		    return false;
		}
	}
	
	/**
	 * Whether a given $value is compliant with a given $baseType.
	 * 
	 * @param int $baseType A value from the BaseType enumeration.
	 * @param mixed $value A value.
	 * @return boolean
	 */
	public static function isBaseTypeCompliant($baseType, $value) {
		
		if ($value === null) {
			return true; // A value can always be null.
		}
		else if ($value instanceof QtiDatatype && $baseType === $value->getBaseType()) {
		    return true;
		}
		else {
		    return false;
		}
	}
	
	public static function isCardinalityCompliant($cardinality, $value) {
	    if ($value === null) {
	        return true;
	    }
	    else if ($value instanceof QtiDatatype && $cardinality === $value->getCardinality()) {
	        return true;
	    }
	    else {
	        return false;
	    }
	}
	
	/**
	 * Throw an InvalidArgumentException depending on a PHP in-memory value.
	 *
	 * @param mixed $value A given PHP primitive value.
	 * @throws InvalidArgumentException In any case.
	 */
	public static function throwTypingError($value) {
		$givenValue = (gettype($value) == 'object') ? get_class($value) : gettype($value);
		$acceptedTypes = array('boolean', 'integer', 'float', 'double', 'string', 'Duration', 'Pair', 'DirectedPair', 'Point');
		$acceptedTypes = implode(", ", $acceptedTypes);
		$msg = "A value is not compliant with the QTI runtime model datatypes: ${acceptedTypes} . '${givenValue}' given.";
		throw new InvalidArgumentException($msg);
	}
	
	/**
	 * Throw an InvalidArgumentException depending on a given qti:baseType
	 * and an in-memory PHP value.
	 *
	 * @param int $baseType A value from the BaseType enumeration.
	 * @param mixed $value A given PHP primitive value.
	 * @throws InvalidArgumentException In any case.
	 */
	public static function throwBaseTypeTypingError($baseType, $value) {
		$givenValue = (gettype($value) == 'object') ? get_class($value) : gettype($value) . ':' . $value;
		$acceptedTypes = BaseType::getNameByConstant($baseType);
		$msg = "The value '${givenValue}' is not compliant with the '${acceptedTypes}' baseType.";
		throw new InvalidArgumentException($msg);
	}
	
	/**
	 * Infer the QTI baseType of a given $value.
	 * 
	 * @param mixed $value A value you want to know the QTI baseType.
	 * @return integer|false A value from the BaseType enumeration or false if the baseType could not be infered.
	 */
	public static function inferBaseType($value) {
		if ($value === null) {
		    return false;
		}
		else if ($value instanceof RecordContainer) {
		    return false;
		}
		else if ($value instanceof QtiDatatype) {
		    return $value->getBaseType();
		}
		else {
		    return false;
		}
	}
	
	/**
	 * Infer the cardinality of a given $value.
	 * 
	 * Please note that:
	 * 
	 * * A RecordContainer has no cardinality, thus it always returns false for such a container.
	 * * The null value has no cardinality, this it always returns false for such a value. 
	 * 
	 * @param mixed $value A value you want to infer the cardinality.
	 * @return integer|boolean A value from the Cardinality enumeration or false if it could not be infered.
	 */
	public static function inferCardinality($value) {
		if ($value === null) {
		    return false;
		}
		else if ($value instanceof QtiDatatype) {
		    return $value->getCardinality();
		}
		else {
		    return false;
		}
	}
	
	/**
	 * Whether a given $string is a valid variable identifier.
	 * 
	 * Q01			-> Valid
	 * Q_01			-> Valid
	 * 1_Q01		-> Invalid
	 * Q01.SCORE	-> Valid
	 * Q-01.1.Score	-> Valid
	 * Q*01.2.Score	-> Invalid
	 * 
	 * @param string $string A string value.
	 * @return boolean Whether the given $string is a valid variable identifier.
	 */
	public static function isValidVariableIdentifier($string) {
		
		if (gettype($string) !== 'string' || empty($string)) {
			return false;
		}
		
		$pattern = '/^[a-z][a-z0-9_\-]*(?:(?:\.[1-9][0-9]*){0,1}(?:\.[a-z][a-z0-9_\-]*){0,1}){0,1}$/iu';
		return preg_match($pattern, $string) === 1;
	}
	
	/**
	 * Makes $value compliant with baseType $targetBaseType, if $value is compliant. Otherwise,
	 * the original $value is returned.
	 * 
	 * @param mixed $value A QTI Runtime compliant value.
	 * @param integer $targetBaseType The target baseType.
	 * @return mixed The juggled value if needed, otherwise the original value of $value.
	 */
	public static function juggle($value, $targetBaseType) {
	    // A lot of people designing QTI items want to put float values
	    // in integer baseType'd variables... So let's go for type juggling!
	    
	    $valueBaseType = RuntimeUtils::inferBaseType($value);
	    
	    if ($valueBaseType !== $targetBaseType && ($value instanceof MultipleContainer || $value instanceof OrderedContainer)) {
	        
	        $class = get_class($value);
	        
	        if ($valueBaseType === BaseType::FLOAT && $targetBaseType === BaseType::INTEGER) {
	            $value = new $class($targetBaseType, self::floatArrayToInteger($value->getArrayCopy()));
	        }
	        else if ($valueBaseType === BaseType::INTEGER && $targetBaseType === BaseType::FLOAT) {
	            $value = new $class($targetBaseType, self::integerArrayToFloat($value->getArrayCopy()));
	        }
	        else if ($valueBaseType === BaseType::IDENTIFIER && $targetBaseType === BaseType::STRING) {
	            $value = new $class($targetBaseType, $value->getArrayCopy());
	        }
	        else if ($valueBaseType === BaseType::STRING && $targetBaseType === BaseType::IDENTIFIER) {
	            $value = new $class($targetBaseType, $value->getArrayCopy());
	        }
	        else if ($valueBaseType === BaseType::URI && $targetBaseType === BaseType::STRING) {
	            $value = new $class($targetBaseType, $value->getArrayCopy());
	        }
	        else if ($valueBaseType === BaseType::STRING && $targetBaseType === BaseType::URI) {
	            $value = new $class($targetBaseType, $value->getArrayCopy());
	        }
	        else if ($valueBaseType === BaseType::URI && $targetBaseType === BaseType::IDENTIFIER) {
	            $value = new $class($targetBaseType, $value->getArrayCopy());
	        }
	        else if ($valueBaseType === BaseType::IDENTIFIER && $targetBaseType === BaseType::URI) {
	            $value = new $class($targetBaseType, $value->getArrayCopy());
	        }
	    }
	    else if ($valueBaseType !== $targetBaseType) {
	        // Scalar value.
	        if ($valueBaseType === BaseType::FLOAT && $targetBaseType === BaseType::INTEGER) {
	            $value = intval($value);
	        }
	        else if ($valueBaseType === BaseType::INTEGER && $targetBaseType === BaseType::FLOAT) {
	            $value = floatval($value);
	        }
	    }

	    return $value;
	}
	
	/**
	 * Check whether or not $firstBaseType is compliant with $secondBaseType.
	 * 
	 * The following associations of baseTypes are considered to be compliant:
	 * 
	 * * identifier - string
	 * * string - identifier
	 * * uri - string
	 * * string - uri
	 * * uri - identifier
	 * * identifier - uri
	 * * string - intOrIdentifier
	 * * integer - intOrIdentifier
	 * * identifier - intOrIdentifier
	 * 
	 * @param integer $firstBaseType A value from the baseType enumeration.
	 * @param integer $secondBaseType A value from the baseType enumeration.
	 * @return boolean
	 */
	public static function areBaseTypesCompliant($firstBaseType, $secondBaseType) {
	    if ($firstBaseType === $secondBaseType) {
	        return true;
	    }
	    else if ($firstBaseType === BaseType::IDENTIFIER && $secondBaseType === BaseType::STRING) {
	        return true;
	    }
	    else if ($firstBaseType === BaseType::STRING && $secondBaseType === BaseType::IDENTIFIER) {
	        return true;
	    }
	    else if ($firstBaseType === BaseType::URI && $secondBaseType === BaseType::STRING) {
	        return true;
	    }
	    else if ($firstBaseType === BaseType::STRING && $secondBaseType === BaseType::URI) {
	        return true;
	    }
	    else if ($firstBaseType === BaseType::URI && $secondBaseType === BaseType::IDENTIFIER) {
	        return true;
	    }
	    else if ($firstBaseType === BaseType::IDENTIFIER && $secondBaseType === BaseType::URI) {
	        return true;
	    }
	    else if ($firstBaseType === BaseType::STRING && $secondBaseType === BaseType::INT_OR_IDENTIFIER) {
	        return true;
	    }
	    else if ($firstBaseType === BaseType::INTEGER && $secondBaseType === BaseType::INT_OR_IDENTIFIER) {
	        return true;
	    }
	    else if ($firstBaseType === BaseType::IDENTIFIER && $secondBaseType === BaseType::INT_OR_IDENTIFIER) {
	        return true;
	    }
	    
	    return false;
	}
	
	/**
	 * Transforms the content of float array to an integer array.
	 * 
	 * @param array $floatArray An array containing float values.
	 * @return array An array containing integer values.
	 */
	public static function floatArrayToInteger($floatArray) {
	    $integerArray = array();
	    foreach ($floatArray as $f) {
	        $integerArray[] = (is_null($f) === false) ? intval($f) : null;
	    }
	    return $integerArray;
	}
	
	/**
	 * Transforms the content of an integer array to a float array.
	 * 
	 * @param array $integerArray An array containing integer values.
	 * @return array An array containing float values.
	 */
	public static function integerArrayToFloat($integerArray) {
	    $floatArray = array();
	    foreach ($integerArray as $i) {
	        $floatArray[] = (is_null($i) === false) ? floatval($i) : null;
	    }
	    return $floatArray;
	}
	
	public static function valueToRuntime($v, $baseType) {
	    
	    if ($v !== null) {
	        
	        if (is_int($v) === true) {
	             
	            if ($baseType === -1 || $baseType === BaseType::INTEGER) {
	                return new Integer($v);
	            }
	            else if ($baseType === BaseType::INT_OR_IDENTIFIER) {
	                return new IntOrIdentifier($v);
	            }
	        }
	        else if (is_string($v) === true) {
	            
	            if ($baseType === BaseType::IDENTIFIER) {
	                return new Identifier($v);
	            }
	            if ($baseType === -1 || $baseType === BaseType::STRING) {
	                return new String($v);
	            }
	            else if ($baseType === BaseType::URI) {
	                return new Uri($v);
	            }
	            else if ($baseType === BaseType::INT_OR_IDENTIFIER) {
	                return new IntOrIdentifier($v);
	            }
	        }
	        else if (is_float($v) === true) {
	            return new Float($v);
	        }
	        else if (is_bool($v) === true) {
	            return new Boolean($v);
	        }
	        
	    }
	    
	    return $v;
	}
}