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
use \InvalidArgumentException;
use \OutOfBoundsException;

/**
 * The BinaryStream class represents a binary stream based on a binary 
 * string.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class BinaryStream implements IStream {
    
    /**
     * The binary string to be read.
     * 
     * @var string
     */
    private $binary = '';
    
    /**
     * Whether the stream is open.
     * 
     * @var boolean
     */
    private $open = false;
    
    /**
     * The position in the stream.
     * 
     * @var integer
     */
    private $position = 0;
    
    /**
     * Create a new BinaryStream object.
     * 
     * @param string $binary A binary string.
     * @throws InvalidArgumentException If $binary is not a string value.
     */
    public function __construct($binary = '') {
        $this->setBinary($binary);
    }
    
    /**
     * Set the binary string that is composing the data stream.
     * 
     * @param string $binary A binary string.
     * @throws InvalidArgumentException If $binary is not a string.
     */
    protected function setBinary($binary) {
        if (gettype($binary) !== 'string') {
            $msg = "The 'binary' argument must be a string, '" . gettype($binary) . "' given.";
            throw new InvalidArgumentException($msg);
        }
        
        $this->binary = $binary;
    }
    
    /**
     * Get the binary string that is composing the data stream.
     * 
     * @return string A binary string.
     */
    public function getBinary() {
        return $this->binary;
    }
    
    /**
     * Returns the current position in the stream.
     * 
     * @return integer The position in the stream. Position begins at 0.
     */
    public function getPosition() {
        return $this->position;
    }
    
    /**
     * Set the current position in the stream.
     * 
     * @param integer $position A position in the stream to be set.
     */
    protected function setPosition($position) {
        $this->position = $position;
    }
    
    /**
     * Increment the current position by $i.
     * 
     * @param integer $i The increment to be applied on the current position in the stream.
     * @throws OutOfBoundsException If the new position falls outside the bounds of the stream.
     */
    protected function incrementPosition($i) {
        $incPos = $this->getPosition() + $i;
        if ($incPos > $this->getLength()) {
            $msg = "Incremented position '${incPos}' is outside the bounds of the stream.";
            throw new OutOfBoundsException($msg);
        }
        
        $this->setPosition($incPos);
    }
    
    /**
     * Open the binary stream.
     * 
     * @throws BinaryStreamException If the stream is already opened.
     */
    public function open() {
        if ($this->isOpen() === true) {
            $msg = "The BinaryStream is already open.";
            throw new BinaryStreamException($msg, $this, BinaryStreamException::ALREADY_OPEN);
        }
        
        $this->setOpen(true);
    }
    
    /**
     * Close the binary stream.
     * 
     * @throws BinaryStreamException If the stream is closed prior the call.
     */
    public function close() {
        if ($this->isOpen() === false) {
            $msg = "Cannot call close() a closed stream.";
            throw new BinaryStreamException($msg, $this, BinaryStreamException::NOT_OPEN);
        }
        
        $this->setOpen(false);
    }
    
    /**
     * Read $length bytes from the BinaryStream.
     * 
     * @throws BinaryStreamException If the stream is closed or if there is no such $length bytes to be read or if EOF already reached.
     * @return string The read value or an empty string if length = 0.
     */
    public function read($length) {
        
        if ($length === 0) {
            return '';
        }
        else if ($this->isOpen() === false) {
            $msg = "Cannot call read() on a closed stream";
            throw new BinaryStreamException($msg, $this, BinaryStreamException::NOT_OPEN);
        }
        else if ($length > 0 && $this->eof() === true) {
            $msg = "Cannot call read() while EOF reached.";
            throw new BinaryStreamException($msg, $this, BinaryStreamException::READ);
        }
        
        $position = $this->getPosition();
        $finalPosition = $position + $length;
        $binary = $this->getBinary();
        
        if ($finalPosition > $this->getLength()) {
            $msg = "Cannot read outside the bounds of the BinaryStream.";
            throw new BinaryStreamException($msg, $this, BinaryStreamException::READ);
        }
        
        $this->incrementPosition($length);
        return substr($binary, $position, $length);
    }
    
    /**
     * Write some $data in the stream.
     * 
     * @throws BinaryStreamException If the BinaryStream is not open.
     */
    public function write($data) {
        
        if ($this->isOpen() === false) {
            $msg = "Cannot call write() on a closed stream.";
            throw new BinaryStreamException($msg, $this, BinaryStreamException::NOT_OPEN);
        }
        
        $position = $this->getPosition();
        $binary = $this->getBinary();
        
        if ($this->getLength() - 1 === $position) {
            // simply append.
            $this->setBinary($binary . $data);
        }
        else if ($position === 0) {
            // simply prepend.
            $this->setBinary($data . $binary);
        }
        else {
            // we are in the middle of the string.
            $part1 = substr($binary, 0, $position);
            $part2 = substr($binary, $position);
            $this->setBinary($part1 . $data . $part2);
        }
        
        $dataLen = strlen($data);
        $this->incrementPosition($dataLen);
        return $dataLen;
    }
    
    /**
     * Whether the end of the binary stream is reached.
     * 
     * @return boolean
     * @throws BinaryStreamException If the binary stream is not open.
     */
    public function eof() {
        if ($this->isOpen() === false) {
            $msg = "Cannot call eof() on a closed BinaryStream.";
            throw new BinaryStreamException($msg, $this, BinaryStreamException::NOT_OPEN);
        }
        
        return $this->getPosition() >= $this->getLength();
    }
    
    /**
     * Whether the stream is open yet.
     * 
     * @return boolean
     */
    public function isOpen() {
        return $this->open;
    }
    
    /**
     * Rewind the stream to its initial position.
     * 
     * @throws BinaryStreamException If the binary stream is not open.
     */
    public function rewind() {
        if ($this->isOpen() === false) {
            $msg = "Cannot call rewind() on a closed Binary Stream.";
            throw new BinaryStreamException($msg, $this, BinaryStreamException::NOT_OPEN);
        }
        
        $this->setPosition(0);
    }
    
    /**
     * Specify whether or not the stream is open.
     * 
     * @param boolean $open
     */
    protected function setOpen($open) {
        $this->open = $open;
    }
    
    /**
     * Get the length of the binary data composing the binary stream.
     * 
     * @return integer The length of the binary data composing the binary stream.
     */
    public function getLength() {
        return strlen($this->getBinary());
    }
}