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
use qtism\data\storage\php\PhpVariable;
use qtism\data\storage\php\PhpArgument;
use qtism\data\storage\php\PhpArgumentCollection;

/**
 * Implements the logic of marshalling PHP arrays into
 * PHP source code.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PhpArrayMarshaller extends PhpMarshaller {
    
    /**
     * Marshall an array into PHP source code.
     * 
     * @throws PhpMarshallingException If something wrong happens during marshalling.
     */
    public function marshall() {
        $ctx = $this->getContext();
        $access = $ctx->getStreamAccess();
        $array = $this->getToMarshall();
        $args = new PhpArgumentCollection();
        
        foreach ($array as $a) {
            if (PhpUtils::isScalar($a) === false) {
                $msg = "The PhpArrayMarshaller class only deals with PHP scalar values, object or resource given.";
                throw new PhpMarshallingException($msg);
            }
            
            $args[] = new PhpArgument($a);
        }
        
        $arrayVarName = $ctx->generateVariableName($array);
        $access->writeVariable($arrayVarName);
        $access->writeEquals($ctx->mustFormatOutput());
        $access->writeFunctionCall('array', $args);
        $access->writeSemicolon($ctx->mustFormatOutput());
        
        $ctx->pushOnVariableStack($arrayVarName);
    }
    
    /**
     * Whether the $toMarshall value is marshallable by this implementation which
     * only supports arrays to be marshalled.
     * 
     * @return boolean
     */
    protected function isMarshallable($toMarshall) {
        return is_array($toMarshall);
    }
}