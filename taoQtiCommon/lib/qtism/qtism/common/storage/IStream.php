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
namespace qtism\common\storage;

/**
 * The interface a class able to read a Stream must implement.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
interface IStream {
    
    /**
     * Open the stream.
     * 
     * @throws StreamException If an error occurs while opening the stream. The error code will be StreamException::OPEN or StreamException::ALREADY_OPEN.
     */
    public function open();
    
    /**
     * Whether the stream is open.
     * 
     * @return boolean
     */
    public function isOpen();
    
    /**
     * Write $data into the stream.
     * 
     * @param string $data The data to be written in the stream.
     * @return integer The length of the written $data.
     * @throws StreamException If an error occurs while writing the stream. The error code will be StreamException::WRITE or StreamException::NOT_OPEN.
     */
    public function write($data);
    
    /**
     * Close the stream.
     * 
     * @throws StreamException If an error occurs while closing the stream. The error code will be StreamException::CLOSE or StreamException::NOT_OPEN.
     */
    public function close();
    
    /**
     * Read $length bytes from the stream.
     * 
     * @param integer $length The length in bytes of the data to be read from the stream.
     * @throws StreamException If an error occurs while reading the stream. The error code will be StreamException::READ or StreamException::NOT_OPEN.
     */
    public function read($length);
    
    /**
     * Rewind the stream as its beginning.
     * 
     * @throws StreamException If an error occurs during the rewind call. The error code will be StreamException::REWIND or StreamException::NOT_OPEN.
     */
    public function rewind();
    
    /**
     * Whether the end of the stream is reached.
     * 
     * @return boolean
     * @throws StreamException If the stream is not open. The error code will be StreamException::NOT_OPEN;
     */
    public function eof();
    
    /**
     * Flushes the stream. In other words, the content of the stream is empty after
     * calling this method. A call to flush automatically rewinds the stream to its
     * very begining.
     * 
     * @throws StreamException If an error occurs during the flush.
     * @see IStream::rewind()
     */
    public function flush();
}