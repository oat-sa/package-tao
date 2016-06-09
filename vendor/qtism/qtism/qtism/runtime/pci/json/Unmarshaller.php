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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 * 
 *
 */

namespace qtism\runtime\pci\json;

use qtism\common\datatypes\files\FileManager;
use qtism\common\datatypes\files\FileSystemFile;
use qtism\common\datatypes\Point;
use qtism\common\datatypes\Float;
use qtism\common\datatypes\Identifier;
use qtism\common\datatypes\IntOrIdentifier;
use qtism\common\datatypes\Uri;
use qtism\common\datatypes\Duration;
use qtism\common\datatypes\DirectedPair;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\String;
use qtism\common\datatypes\Integer;
use qtism\common\datatypes\Boolean;
use qtism\runtime\common\RecordContainer;
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\State;
use qtism\common\utils\Arrays;
use qtism\common\utils\Format;
use \InvalidArgumentException;

/**
 * This class aims at providing the necessary behaviours to
 * unmarshall JSON PCI representations of QTI data into the QTISM Runtime model.
 * 
 * The JSON data given to this implementation must respect the structure formulated 
 * by the IMS Global Portable Custom Interaction Version 1.0 Candidate Final specification
 * in order to be correctly handled.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @see http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Unmarshaller {
    
    /**
     * A FileManager object making the JSON Unmarshaller able to build
     * QTI Files from a PCI JSON representation.
     * 
     * @var FileManager
     */
    private $fileManager;
    
    /**
     * Create a new JSON Unmarshaller object.
     * 
     * @param FileManager A FileManager object making the unmarshaller able to build QTI Files from PCI JSON representation.
     */
    public function __construct(FileManager $fileManager) {
        $this->setFileManager($fileManager);
    }
    
    /**
     * Set the FileManager object making the Unmarshaller able to build QTI Files from
     * a PCI JSON representation.
     * 
     * @param FileManager $fileManager A FileManager object.
     */
    protected function setFileManager(FileManager $fileManager) {
        $this->fileManager = $fileManager;
    }
    
    /**
     * Get the FileManager object making the Unmarshaller able to build QTI Files from
     * a PCI JSON representation.
     * 
     * @return FileManager A FileManager object.
     */
    protected function getFileManager() {
        return $this->fileManager;
    }
    
    /**
     * Transform a PCI JSON representation of QTI data into the QTISM runtime model.
     * 
     * @param string|array $json The json data to be transformed.
     * @throws UnmarshallingException If an error occurs while processing $json.
     * @return null|qtism\common\datatypes\QtiDataType|array
     */
    public function unmarshall($json) {
        if (is_string($json) === true) {
            
            $tmpJson = @json_decode($json, true);
            if ($tmpJson === null) {
                // An error occured while decoding.
                $msg = "An error occured while decoding the following JSON data '" . mb_substr($json, 0, 30, 'UTF-8') . "...'.";
                $code = UnmarshallingException::JSON_DECODE;
                throw new UnmarshallingException($msg, $code);
            }
            
            $json = $tmpJson;
        }
        
        if (is_array($json) === false || count($json) === 0) {
            $msg = "The '" . get_class($this) . "::unmarshall' method only accepts a JSON string or a non-empty array as argument, '";
            if (is_object($json) === true) {
                $msg .= get_class($json);
            } 
            else {
                $msg .= gettype($json);
            }
            
            $msg .= "' given.";
            $code = UnmarshallingException::NOT_SUPPORTED;
            throw new UnmarshallingException($msg, $code);
        }
        
        if (Arrays::isAssoc($json) === false) {
            $msg = "The '" . get_class($this) . "::unmarshall' does not accepts non-associative arrays.";
            $code = UnmarshallingException::NOT_SUPPORTED;
            throw new UnmarshallingException($msg, $code);
        }
        
        // Check whether or not $json is a state (no 'base' nor 'list' keys found),
        // a base, a list or a record.
        $keys = array_keys($json);
        
        if (in_array('base', $keys) === true) {
            // This is a base.
            return $this->unmarshallUnit($json);
        }
        else if (in_array('list', $keys) === true) {
            
            $keys = array_keys($json['list']);
            if (isset($keys[0]) === false) {
                $msg = "No baseType provided for list.";
                throw new UnmarshallingException($msg, UnmarshallingException::NOT_PCI);
            }
            
            $baseType = BaseType::getConstantByName($keys[0]);
            
            if ($baseType === false) {
                $msg = "Unknown QTI baseType '" . $keys[0] . "'.";
                $code = UnmarshallingException::NOT_PCI;
                throw new UnmarshallingException($msg, $code);
            }
            
            $returnValue = new MultipleContainer($baseType);
            
            // This is a list.
            foreach ($json['list'][$keys[0]] as $v) {
                try {
                    if ($v === null) {
                        $returnValue[] = $this->unmarshallUnit(array('base' => $v));
                    }
                    else {
                        $returnValue[] = $this->unmarshallUnit(array('base' => array($keys[0] => $v)));
                    }
                }
                catch (InvalidArgumentException $e) {
                    $strBaseType = BaseType::getNameByConstant($baseType);
                    $msg = "A value is not compliant with the '${strBaseType}' baseType.";
                    $code = UnmarshallingException::NOT_PCI;
                    throw new UnmarshallingException($msg, $code);
                }
            }
            
            return $returnValue;
        }
        else if (in_array('record' , $keys) === true) {
            // This is a record.
            $returnValue = new RecordContainer();
            
            if (count($json['record']) === 0) {
                return $returnValue;
            }
            
            foreach ($json['record'] as $v) {
                if (isset($v['name']) === false) {
                    $msg = "No 'name' key found in record field.";
                    $code = UnmarshallingException::NOT_PCI;
                    throw new UnmarshallingException($msg, $code);
                }
                
                if (isset($v['base']) === true || (array_key_exists('base', $v) &&  $v['base'] === null)) {
                    $unit = array('base' => $v['base']);
                }
                else {
                    // No value found, let's go for a null value.
                    $unit = array('base' => null);
                }
                
                $returnValue[$v['name']] = $this->unmarshallUnit($unit);
            }
            
            return $returnValue;
        }
        else {
            // This is a state.
            $state = array();
            
            foreach ($json as $k => $j) {
                $state[$k] = $this->unmarshall($j);
            }
            
            return $state;
        }
    }
    
    /**
     * Unmarshall a unit of data into QTISM runtime model.
     * 
     * @param array $unit
     * @throws UnmarshallingException
     * @return null|qtism\common\datatypes\QtiDatatype
     */
    protected function unmarshallUnit(array $unit) {
        if (isset($unit['base'])) {
            
            if ($unit['base'] === null) {
                return null;
            }
            
            // Primitive base type.
            try {
                $keys = array_keys($unit['base']);
                switch ($keys[0]) {
                    case 'boolean':
                        return $this->unmarshallBoolean($unit);
                    break;
                    
                    case 'integer':
                        return $this->unmarshallInteger($unit);
                    break;
                    
                    case 'float':
                        return $this->unmarshallFloat($unit);
                    break;
                    
                    case 'string':
                        return $this->unmarshallString($unit);
                    break;
                    
                    case 'point':
                        return $this->unmarshallPoint($unit);
                    break;
                    
                    case 'pair':
                        return $this->unmarshallPair($unit);
                    break;
                    
                    case 'directedPair':
                        return $this->unmarshallDirectedPair($unit);
                    break;
                    
                    case 'duration':
                        return $this->unmarshallDuration($unit);
                    break;
                    
                    case 'file':
                        return $this->unmarshallFile($unit);
                    break;
                    
                    case 'uri':
                        return $this->unmarshallUri($unit);
                    break;
                    
                    case 'intOrIdentifier':
                        return $this->unmarshallIntOrIdentifier($unit);
                    break;
                    
                    case 'identifier':
                        return $this->unmarshallIdentifier($unit);
                    break;
                    
                    default:
                        throw new UnmarshallingException("Unknown QTI baseType '" . $keys[0] . "'");
                    break;
                }
            }
            catch (InvalidArgumentException $e) {
                $msg = "A value does not satisfy its baseType.";
                throw new UnmarshallingException($msg, UnmarshallingException::NOT_PCI, $e);
            }
        }
    }
    
    /**
     * Unmarshall a boolean JSON PCI representation.
     * 
     * @param array $unit
     * @return Boolean
     */
    protected function unmarshallBoolean(array $unit) {
        return new Boolean($unit['base']['boolean']);
    }
    
    /**
     * Unmarshall an integer JSON PCI representation.
     * 
     * @param array $unit
     * @return Integer
     */
    protected function unmarshallInteger(array $unit) {
        return new Integer($unit['base']['integer']);
    }
    
    /**
     * Unmarshall a float JSON PCI representation.
     * 
     * @param array $unit
     * @returnFloat
     */
    protected function unmarshallFloat(array $unit) {
        
        if (is_int($unit['base']['float']) === true) {
            $unit['base']['float'] = floatval($unit['base']['float']);
        }  
        
        return new Float($unit['base']['float']);
    }
    
    /**
     * Unmarshall a string JSON PCI representation.
     * 
     * @param array $unit
     * @return String
     */
    protected function unmarshallString(array $unit) {
        return new String($unit['base']['string']);
    }
    
    /**
     * Unmarshall a point JSON PCI representation.
     * 
     * @param array $unit
     * @return Point
     */
    protected function unmarshallPoint(array $unit) {
        return new Point($unit['base']['point'][0], $unit['base']['point'][1]);
    }
    
    /**
     * Unmarshall a pair JSON PCI representation.
     * 
     * @param array $unit
     * @return Pair
     */
    protected function unmarshallPair(array $unit) {
        return new Pair($unit['base']['pair'][0], $unit['base']['pair'][1]);
    }
    
    /**
     * Unmarshall a directed pair JSON PCI representation.
     * 
     * @param array $unit
     * @return DirectedPair
     */
    protected function unmarshallDirectedPair(array $unit) {
        return new DirectedPair($unit['base']['directedPair'][0], $unit['base']['directedPair'][1]);
    }
    
    /**
     * Unmarshall a duration JSON PCI representation.
     * 
     * @param array $unit
     * @return Duration
     */
    protected function unmarshallDuration(array $unit) {
        return new Duration($unit['base']['duration']);
    }
    
    /**
     * Unmarshall a duration JSON PCI representation.
     * 
     * @param array $unit
     * @return AbstractPersistentFile
     */
    protected function unmarshallFile(array $unit) {
        
        $filename = (empty($unit['base']['file']['name']) === true) ? '' : $unit['base']['file']['name'];
        
        return $this->getFileManager()->createFromData(base64_decode($unit['base']['file']['data']), $unit['base']['file']['mime'], $filename);
    }
    
    /**
     * Unmarshall a duration JSON PCI representation.
     * 
     * @param array $unit
     * @return Uri
     */
    protected function unmarshallUri(array $unit) {
        return new Uri($unit['base']['uri']);
    }
    
    /**
     * Unmarshall an intOrIdentifier JSON PCI representation.
     * 
     * @param array $unit
     * @return IntOrIdentifier
     */
    protected function unmarshallIntOrIdentifier(array $unit) {
        return new IntOrIdentifier($unit['base']['intOrIdentifier']);
    }
    
    /**
     * Unmarshall an identifier JSON PCI representation.
     * 
     * @param array $unit
     * @return Identifier
     */
    protected function unmarshallIdentifier(array $unit) {
        return new Identifier($unit['base']['identifier']);
    }
}