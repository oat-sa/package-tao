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
 * @subpackage 
 *
 */
namespace qtism\runtime\storage\binary;

use \Exception;

/**
 * The BinaryStreamAccessException class represents the error
 * that could occur while reading/extracting data from a BinaryStream
 * object.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class BinaryStreamAccessException extends Exception {
    
    /**
     * Unknown error.
     *
     * @var integer
     */
    const UNKNOWN = 0;
    
    /**
     * A closed BinaryStream object is given as the stream to be read.
     *
     * @var integer
     */
    const NOT_OPEN = 1;
    
    /**
     * The AbstractStreamReader that caused the error.
     *
     * @var AbstractStreamReader
     */
    private $source;
    
    /**
     * An error occured while reading|writing a tinyint.
     * 
     * @var integer
     */
    const TINYINT = 2;
    
    /**
     * An error occured while reading|writing a short int.
     * 
     * @var integer
     */
    const SHORT = 3;
    
    /**
     * An error occured while reading|writing an int.
     * 
     * @var integer
     */
    const INT = 4;
    
    /**
     * An error occured while reading|writing a float.
     * 
     * @var integer
     */
    const FLOAT = 5;
    
    /**
     * An error occured while reading|writing a boolean.
     * 
     * @var integer
     */
    const BOOLEAN = 6;
    
    /**
     * An error occured while reading|writing a string.
     * 
     * @var integer
     */
    const STRING = 7;
    
    /**
     * An error occured while reading|writing binary data.
     * 
     * @var integer
     */
    const BINARY = 8;
    
    /**
     * An error occured while reading|writing a DateTime.
     * 
     * @var integer
     */
    const DATETIME = 9;
    
    /**
     * Create a new BinaryStreamAccessException object.
     *
     * @param string $message A human-readable message.
     * @param BinaryStreamAccess $source The BinaryStreamAccess object that caused the error.
     * @param integer $code An exception code. See class constants.
     * @param Exception $previous An optional previously thrown exception.
     */
    public function __construct($message, BinaryStreamAccess $source, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->setSource($source);
    }
    
    /**
     * Get the BinaryStreamAccess object that caused the error.
     *
     * @param BinaryStreamAccess $source A BinaryStreamAccess object.
     */
    protected function setSource(BinaryStreamAccess $source) {
        $this->source = $source;
    }
    
    /**
     * Set the BinaryStreamAccess object that caused the error.
     *
     * @return BinaryStreamAccess A BinaryStreamAccess object.
     */
    public function getSource() {
        return $this->source;
    }
}