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
namespace qtism\runtime\storage\binary;

use qtism\common\storage\BinaryStreamAccess;
use qtism\common\storage\BinaryStreamAccessException;

use \Exception;

class QtiBinaryStreamAccessException extends BinaryStreamAccessException {
    
    /**
     * An error occured while reading/writing a Variable.
     * 
     * @var integer
     */
    const VARIABLE = 10;
    
    /**
     * An error occured while reading/writing a Record Field.
     * 
     * @var integer
     */
    const RECORDFIELD = 11;
    
    /**
     * An error occured while reading/writing a QTI identifier.
     * 
     * @var integer
     */
    const IDENTIFIER = 12;
    
    /**
     * An error occured while reading/writing a QTI point.
     * 
     * @var integer
     */
    const POINT = 13;
    
    /**
     * An error occured while reading/writing a QTI pair.
     * 
     * @var integer
     */
    const PAIR = 14;
    
    /**
     * An error occured while reading/writing a QTI directedPair.
     * 
     * @var integer
     */
    const DIRECTEDPAIR = 15;
    
    /**
     * An error occured while reading/writing a QTI duration.
     * 
     * @var integer
     */
    const DURATION = 16;
    
    /**
     * An error occured while reading/writing a URI.
     * 
     * @var integer
     */
    const URI = 17;
    
    /**
     * An error occured while reading/writing File's binary data.
     * 
     * @var integer
     */
    const FILE = 18;
    
    /**
     * An error occured while reading/writing an intOrIdentifier.
     * 
     * @var integer
     */
    const INTORIDENTIFIER = 19;
    
    /**
     * An error occured while reading/writing an assessment item session.
     * 
     * @var integer
     */
    const ITEM_SESSION = 20;
    
    /**
     * An error occured while reading/writing a route item.
     * 
     * @var integer
     */
    const ROUTE_ITEM = 21;
    
    /**
     * An error occured while reading/writing pending responses.
     * 
     * @var integer
     */
    const PENDING_RESPONSES = 22;
    
    /**
     * Create a new QtiBinaryStreamAccessException object.
     *
     * @param string $message A human-readable message.
     * @param BinaryStreamAccess $source The BinaryStreamAccess object that caused the error.
     * @param integer $code An exception code. See class constants.
     * @param Exception $previous An optional previously thrown exception.
     */
    public function __construct($message, BinaryStreamAccess $source, $code = 0, Exception $previous = null) {
        parent::__construct($message, $source, $code, $previous);
    }
}