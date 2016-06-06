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
 * The AbstractStreamAccess is the base class of all classes that have
 * to access a stream.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class AbstractStreamAccess {
    
    /**
     * The IStream object to read.
     *
     * @var IStream.
     */
    private $stream;
    
    /**
     * Create a new AbstractStreamAccess object.
     *
     * @param IStream $stream An IStream object to be read.
     * @throws StreamAccessException If $stream is not open yet.
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
     * @throws StreamAccessException If the $stream is not open yet.
     */
    protected function setStream(IStream $stream) {
    
        if ($stream->isOpen() === false) {
            $msg = "An AbstractStreamAccess do not accept closed streams to be read.";
            throw new StreamAccessException($msg, $this, StreamAccessException::NOT_OPEN);
        }
    
        $this->stream = $stream;
    }
}