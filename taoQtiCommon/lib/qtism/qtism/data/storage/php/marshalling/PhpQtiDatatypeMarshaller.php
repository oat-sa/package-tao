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

use qtism\data\storage\php\PhpVariable;
use qtism\data\storage\php\PhpArgument;
use qtism\data\storage\php\PhpArgumentCollection;
use qtism\common\datatypes\Shape;
use qtism\common\datatypes\Point;
use qtism\common\datatypes\Duration;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\Coords;
use qtism\common\datatypes\QtiDatatype;

class PhpQtiDatatypeMarshaller extends PhpMarshaller {
    
    /**
     * Marshall a QtiDatatype object into PHP source code.
     * 
     * @throws PhpMarshallingException If an error occurs during marshalling.
     */
    public function marshall() {
        $toMarshall = $this->getToMarshall();

        try {
            if ($toMarshall instanceof Coords) {
                $this->marshallCoords();
                return;
            }
            else if ($toMarshall instanceof Pair) {
                $this->marshallPair();
                return;
            }
            else if ($toMarshall instanceof Duration) {
                $this->marshallDuration();
                return;
            }
            else if ($toMarshall instanceof Point) {
                $this->marshallPoint();
                return;
            }
        }
        catch (PhpMarshallingException $e) {
            $msg = "An error occured while marshalling a QtiDatatype object.";
            throw new PhpMarshallingException($msg, PhpMarshallingException::RUNTIME, $e);
        }
        
        $className = get_class($toMarshall);
        $msg = "Cannot deal with QtiDatatype '${className}'.";
        throw new PhpMarshallingException($msg, PhpMarshallingException::RUNTIME);
    }
    
    /**
     * Whether the $toMarshall value can be marshalled by this implementation.
     * 
     * @return boolean.
     */
    protected function isMarshallable($toMarshall) {
        return $toMarshall instanceof QtiDatatype;
    }
    
    /**
     * Marshall a Coords QTI datatype object.
     * 
     * @throws PhpMarshallingException
     */
    protected function marshallCoords() {
        // Retrieve the coordinates array.
        $coords = $this->getToMarshall();
        $array = $coords->getArrayCopy();
        
        // Retrieve the shape.
        $shape = $coords->getShape();
        
        // -- Actual marshalling.
        $ctx = $this->getContext();
        $access = $ctx->getStreamAccess();
        
        // Marshall the $array.
        $args = new PhpArgumentCollection();
        foreach ($array as $a) {
            $args[] = new PhpArgument($a);
        }
        
        try {
            $arrayVarName = $ctx->generateVariableName($array);
            $access->writeVariable($arrayVarName);
            $access->writeEquals($ctx->mustFormatOutput());
            $access->writeFunctionCall('array', $args);
            $access->writeSemicolon($ctx->mustFormatOutput());
            
            // Marshall the Coords object.
            $coordsArgs = new PhpArgumentCollection(array(new PhpArgument($coords->getShape()), new PhpArgument(new PhpVariable($arrayVarName))));
            $varName = $ctx->generateVariableName($coords);
            $access->writeVariable($varName);
            $access->writeEquals($ctx->mustFormatOutput());
            $access->writeInstantiation(get_class($coords), $coordsArgs);
            $access->writeSemicolon($ctx->mustFormatOutput());
            
            $ctx->pushOnVariableStack($varName);
        }
        catch (StreamAccessException $e) {
            $msg = "An error occured while marshalling a Coords object into the PHP source code stream.";
            throw new PhpMarshallingException($msg, PhpMarshallingException::STREAM, $e);
        }
    }
    
    /**
     * Marshall a Pair QTI datatype object.
     * 
     * @throws PhpMarshallingException
     */
    protected function marshallPair() {
        $pair = $this->getToMarshall();
        $ctx = $this->getContext();
        $access = $ctx->getStreamAccess();
        
        try {
            $varName = $ctx->generateVariableName($pair);
            $access->writeVariable($varName);
            $access->writeEquals($ctx->mustFormatOutput());
            $args = new PhpArgumentCollection(array(new PhpArgument($pair->getFirst()), new PhpArgument($pair->getSecond())));
            $access->writeInstantiation(get_class($pair), $args);
            $access->writeSemicolon($ctx->mustFormatOutput());
            
            $ctx->pushOnVariableStack($varName);
        }
        catch (StreamAccessException $e) {
            $msg = "An error occured while marshalling a Pair/DirectedPair object into the PHP source code stream.";
            throw new PhpMarshallingException($msg, PhpMarshallingException::STREAM, $e);
        }
    }
    
    /**
     * Marshall a Duration QTI datatype object.
     * 
     * @throws PhpMarshallingException
     */
    protected function marshallDuration() {
        $duration = $this->getToMarshall();
        $ctx = $this->getContext();
        $access = $ctx->getStreamAccess();
        
        try {
            $varName = $ctx->generateVariableName($duration);
            $access->writeVariable($varName);
            $access->writeEquals($ctx->mustFormatOutput());
            $args = new PhpArgumentCollection(array(new PhpArgument($duration->__toString())));
            $access->writeInstantiation(get_class($duration), $args);
            $access->writeSemiColon($ctx->mustFormatOutput());
            
            $ctx->pushOnVariableStack($varName);
        }
        catch (StreamAccessException $e) {
            $msg = "An error occured while marshalling a Duration object into the PHP source code stream.";
            throw new PhpMarshallingException($msg, PhpMarshallingException::STREAM, $e);
        }
    }
    
    /**
     * Marshall a Point QTI datatype object.
     * 
     * @throws PhpMarshallingException
     */
    protected function marshallPoint() {
        $point = $this->getToMarshall();
        $ctx = $this->getContext();
        $access = $ctx->getStreamAccess();
        
        try {
            $varName = $ctx->generateVariableName($point);
            $access->writeVariable($varName);
            $access->writeEquals($ctx->mustFormatOutput());
            $args = new PhpArgumentCollection(array(new PhpArgument($point->getX()), new PhpArgument($point->getY())));
            $access->writeInstantiation(get_class($point), $args);
            $access->writeSemicolon($ctx->mustFormatOutput());
            
            $ctx->pushOnVariableStack($varName);
        }
        catch (StreamAccessException $e) {
            $msg = "An error occured while marshalling a Point object into the PHP source code stream.";
            throw new PhpMarshallingException($msg, PhpMarshallingException::STREAM, $e);
        }
    }
}