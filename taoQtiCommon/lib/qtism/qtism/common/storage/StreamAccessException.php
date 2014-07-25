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

use \Exception;

/**
 * The StreamAccessException class represents the error
 * that could occur while reading/extracting data from an IStream object
 * object.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class StreamAccessException extends Exception {
    
    /**
     * Unknown error.
     *
     * @var integer
     */
    const UNKNOWN = 0;
    
    /**
     * A closed IStream object is given as the stream to be read.
     *
     * @var integer
     */
    const NOT_OPEN = 1;
    
    /**
     * The AbstractStreamAccess object that caused the error.
     *
     * @var AbstractStreamAccess
     */
    private $source;
    
    /**
     * Create a new StreamAccessException object.
     *
     * @param string $message A human-readable message.
     * @param AbstractStreamAccess $source The AbstractStreamAccess object that caused the error.
     * @param integer $code An exception code. See class constants.
     * @param Exception $previous An optional previously thrown exception.
     */
    public function __construct($message, AbstractStreamAccess $source, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->setSource($source);
    }
    
    /**
     * Get the AbstractStreamAccess object that caused the error.
     *
     * @param AbstractStreamAccess $source An AbstractStreamAccess object.
     */
    protected function setSource(AbstractStreamAccess $source) {
        $this->source = $source;
    }
    
    /**
     * Set the AbstractStreamAccess object that caused the error.
     *
     * @return AbstractStreamAccess An AbstractStreamAccess object.
     */
    public function getSource() {
        return $this->source;
    }
}