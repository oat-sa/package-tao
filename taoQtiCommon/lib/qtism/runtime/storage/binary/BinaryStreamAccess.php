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

use qtism\runtime\storage\common\IStream;
use \DateTime;

/**
 * The BinaryStreamAccess aims at providing the needed methods to
 * easily read the data inside BinaryStream objects.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class BinaryStreamAccess {
    
    /**
     * The IStream object to read.
     *
     * @var IStream.
     */
    private $stream;
    
    /**
     * Create a new BinaryStreamAccess object.
     *
     * @param IStream $stream An IStream object to be read.
     * @throws StreamReaderException If $stream is not open yet.
     */
    public function __construct(IStream $stream) {
        $this->setStream($stream);
    }
    
    /**
     * Get the IStream object to be read.
     *
     * @return IStream An IStream object.
     */
    protected function getStream() {
        return $this->stream;
    }
    
    /**
     * Set the IStream object to be read.
     *
     * @param IStream $stream An IStream object.
     * @throws StreamReaderException If the $stream is not open yet.
     */
    protected function setStream(IStream $stream) {
    
        if ($stream->isOpen() === false) {
            $msg = "A BinaryStreamAccess do not accept closed streams to be read.";
            throw new BinaryStreamAccessException($msg, $this, BinaryStreamAccessException::NOT_OPEN);
        }
    
        $this->stream = $stream;
    }
    
    /**
     * Read a single byte unsigned integer from the current binary stream.
     * 
     * @throws BinaryStreamAccessException
     * @return integer
     */
    public function readTinyInt() {
        try {
            $bin = $this->getStream()->read(1);
            return ord($bin);
        }
        catch (BinaryStreamException $e) {
            $this->handleBinaryStreamException($e, BinaryStreamAccessException::TINYINT);
        }
    }
    
    /**
     * Write a single byte unsigned integer in the current binary stream.
     * 
     * @param integer $tinyInt
     * @throws BinaryStreamAccessException
     */
    public function writeTinyInt($tinyInt) {
        try {
            $this->getStream()->write(chr($tinyInt));
        }
        catch (BinaryStreamException $e) {
            $this->handleBinaryStreamException($e, BinaryStreamAccessException::TINYINT, false);
        }
    }
    
    /**
     * Read a 2 bytes unsigned integer from the current binary stream.
     * 
     * @throws BinaryStreamAccessException
     * @return integer
     */
    public function readShort() {
        try {
            $bin = $this->getStream()->read(2);
            return current(unpack('S', $bin));
        }
        catch (BinaryStreamException $e) {
            $this->handleBinaryStreamException($e, BinaryStreamAccessException::SHORT);
        }
    }
    
    /**
     * Write a 2 bytes unsigned integer in the current binary stream.
     * 
     * @param integer $short
     * @throws BinaryStreamAccessException
     */
    public function writeShort($short) {
        try {
            $this->getStream()->write(pack('S', $short));
        }
        catch (BinaryStreamException $e) {
            $this->handleBinaryStreamException($e, BinaryStreamAccessException::SHORT, false);
        }
    }
    
    /**
     * Read a 8 bytes signed integer from the current binary stream.
     * 
     * @throws BinaryStreamAccessException
     * @return integer
     */
    public function readInteger() {
        try {
            $bin = $this->getStream()->read(4);
            return current(unpack('l', $bin));
        }
        catch (BinaryStreamException $e) {
            $this->handleBinaryStreamException($e, BinaryStreamAccessException::INT);
        }
    }
    
    /**
     * Write a 8 bytes signed integer in the current binary stream.
     * 
     * @param integer $int
     * @throws BinaryStreamAccessException
     */
    public function writeInteger($int) {
        try {
            $this->getStream()->write(pack('l', $int));
        }
        catch (BinaryStreamException $e) {
            $this->handleBinaryStreamException($e, BinaryStreamAccessException::INT, false);
        }
    }
    
    /**
     * Read a double precision float from the current binary stream.
     * 
     * @throws BinaryStreamAccessException
     * @return integer
     */
    public function readFloat() {
        try {
            $bin = $this->getStream()->read(8);
            return current(unpack('d', $bin));
        }
        catch (BinaryStreamException $e) {
            $this->handleBinaryStreamException($e, BinaryStreamAccessException::FLOAT);
        }
    }
    
    /**
     * Write a double precision float in the current binary stream.
     * 
     * @param float $float
     * @throws BinaryStreamAccessException
     */
    public function writeFloat($float) {
        try {
            $this->getStream()->write(pack('d', $float));
        }
        catch (BinaryStreamException $e) {
            $this->handleBinaryStreamException($e, BinaryStreamAccessException::FLOAT, false);
        }
    }
    
    /**
     * Read a boolean value from the current binary stream.
     * 
     * @throws BinaryStreamAccessException
     * @return boolean
     */
    public function readBoolean() {
        try {
            $int = ord($this->getStream()->read(1));
            return ($int === 0) ? false : true;
        }
        catch (BinaryStreamException $e) {
            $this->handleBinaryStreamException($e, BinaryStreamAccessException::BOOLEAN);
        }
    }
    
    /**
     * Write a boolean value from the current binary stream.
     * 
     * @param boolean $boolean
     * @throws BinaryStreamAccessException
     */
    public function writeBoolean($boolean) {
        try {
            $val = ($boolean === true) ? 1 : 0;
            $this->getStream()->write(chr($val));
        }
        catch (BinaryStreamAccessException $e) {
            $this->handleBinaryStreamException($e, BinaryStreamAccessException::FLOAT, false);
        }
    }
    
    /**
     * Read a string value from the current binary stream.
     * 
     * @throws BinaryStreamAccessException
     * @return string
     */
    public function readString() {
        try {
            $binLength = $this->getStream()->read(2);
            $length = current(unpack('S', $binLength));
            return $this->getStream()->read($length);
        }
        catch (BinaryStreamException $e) {
            $this->handleBinaryStreamException($e, BinaryStreamAccessException::STRING);
        }
    }
    
    /**
     * Write a string value from in the current binary string.
     * 
     * @param string $string
     * @throws BinaryStreamAccessException
     */
    public function writeString($string) {
        $len = strlen($string);
        try {
            $this->getStream()->write(pack('S', $len) . $string);
        }
        catch (BinaryStreamException $e) {
            $this->handleBinaryStreamException($e, BinaryStreamAccessException::STRING, false);
        }
    }
    
    /**
     * Read binary data from the current binary stream.
     * 
     * @throws BinaryStreamAccessException
     * @return string A binary string.
     */
    public function readBinary() {
        return $this->readString();
    }
    
    /**
     * Write binary data in the current binary stream.
     * 
     * @param string $binary
     * @throws BinaryStreamAccessException
     */
    public function writeBinary($binary) {
        $this->writeString($binary);
    }
    
    /**
     * Read a DateTime from the current binary stream.
     * 
     * @return DateTime A DateTime object.
     * @throws BinaryStreamAccessException
     */
    public function readDateTime() {
        try {
            $timeStamp = current(unpack('l', $this->getStream()->read(4)));
            $date = new DateTime();
            return $date->setTimestamp($timeStamp);
        }
        catch (BinaryStreamException $e) {
            $this->handleBinaryStreamException($e, BinaryStreamAccessException::DATETIME);
        }
    }
    
    /**
     * Write a DateTime from the current binary stream.
     * 
     * @param DateTime $dateTime A DateTime object.
     * @throws BinaryStreamAccessException
     */
    public function writeDateTime(DateTime $dateTime) {
        try {
            $timeStamp = $dateTime->getTimestamp();
            $this->getStream()->write(pack('l', $timeStamp));
        }
        catch (BinaryStreamException $e) {
            $this->handleBinaryStreamException($e, BinaryStreamAccessException::DATETIME, false);
        }
    }
    
    /**
     * Handle a BinaryStreamException in order to throw the relevant BinaryStreamAccessException.
     * 
     * @param BinaryStreamException $e The BinaryStreamException object to deal with.
     * @param integer $typeError The BinaryStreamAccess exception code to be trown in case of READ error.
     * @param boolean $read Wheter the error occured in a reading/writing context.
     * @throws BinaryStreamAccessException The resulting BinaryStreamAccessException.
     */
    protected function handleBinaryStreamException(BinaryStreamException $e, $typeError, $read = true) {
        
        $strType = 'unknown datatype';
        
        switch ($typeError) {
            case BinaryStreamAccessException::BOOLEAN:
                $strType = 'boolean';
            break;
            
            case BinaryStreamAccessException::BINARY:
                $strType = 'binary data';
            break;
            
            case BinaryStreamAccessException::FLOAT:
                $strType = 'double precision float';
            break;
            
            case BinaryStreamAccessException::INT:
                $strType = 'integer';
            break;
            
            case BinaryStreamAccessException::SHORT:
                $strType = 'short integer';
            break;
            
            case BinaryStreamAccessException::STRING:
                $strType = 'string';
            break;
            
            case BinaryStreamAccessException::TINYINT:
                $strType = 'tiny integer';
            break;
            
            case BinaryStreamAccessException::DATETIME:
                $strType = 'datetime';
            break;
        }
        
        $strAction = ($read === true) ? 'reading' : 'writing';
        
        switch ($e->getCode()) {
            case BinaryStreamException::NOT_OPEN:
                $strAction = ucfirst($strAction);
                $msg = "${strAction} a ${strType} from a closed binary stream is impossible.";
                throw new BinaryStreamAccessException($msg, $this, BinaryStreamAccessException::NOT_OPEN, $e);
                break;
        
            case BinaryStreamException::READ:
                $msg = "An error occured while ${strAction} a ${strType}.";
                throw new BinaryStreamAccessException($msg, $this, $typeError, $e);
                break;
        
            default:
                $msg = "An unknown error occured while ${strAction} a ${strType}.";
                throw new BinaryStreamAccessException($msg, $this, BinaryStreamAccessException::UNKNOWN, $e);
                break;
        }
    }
}