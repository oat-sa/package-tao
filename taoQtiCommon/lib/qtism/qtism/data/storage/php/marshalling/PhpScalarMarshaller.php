<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package
 */

namespace qtism\data\storage\php\marshalling;

use qtism\data\storage\php\Utils as PhpUtils;

/**
 * This implementation of PhpMarshaller focuses on marshalling PHP scalar
 * values (including the null value) into PHP source code.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PhpScalarMarshaller extends PhpMarshaller {
    
    /**
     * Create a new PhpScalarMarshaller objec.
     * 
     * @param PhpMarshallingContext $context A PhpMarshallingContext object.
     * @param mixed PHP scalar value (including null value) to be marshalled.
     * @throws InvalidArgumentException If $toMarshall is not considered to be a marshallable PHP scalar value.
     */
    public function __construct(PhpMarshallingContext $context, $toMarshall) {
        parent::__construct($context, $toMarshall);
    }
    
    /**
     * Checks whether or not the given value is marshallable by this implementation.
     * 
     * @return boolean
     */
    protected function isMarshallable($toMarshall) {
        return PhpUtils::isScalar($toMarshall);
    }
    
    /**
     * Marshall the PHP scalar value to be marshalled into PHP source code.
     * 
     * @throws PhpMarshallingException If an error occurs while marshalling.
     */
    public function marshall() {
        $ctx = $this->getContext();
        $streamAccess = $ctx->getStreamAccess();
        
        try {
            $scalar = $this->getToMarshall();
            $varName = $this->getContext()->generateVariableName($scalar);
            
            $streamAccess->writeVariable($varName);
            $streamAccess->writeEquals($ctx->mustFormatOutput());
            $streamAccess->writeScalar($scalar);
            $streamAccess->writeSemicolon($ctx->mustFormatOutput());
            
            $ctx->pushOnVariableStack($varName);
        }
        catch (StreamAccessException $e) {
            $msg = "An error occured while marshalling the scalar value '${scalar}'.";
            throw new PhpMarshallingException($msg, PhpMarshallingException::STREAM, $e);
        }
    }
}