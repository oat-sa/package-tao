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
namespace qtism\runtime\tests;

use qtism\common\enums\Cardinality;

use qtism\common\enums\BaseType;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\common\State;
use \InvalidArgumentException;

/**
 * A specialized State implementation aiming at storing 'duration' OutcomeVariable objects.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class DurationStore extends State {
    
    /**
     * Checks whether or not $value:
     * 
     * * is an instance of OutcomeVariable
     * * has a 'duration' QTI baseType.
     * * has 'single' QTI cardinality.
     * 
     * @throws InvalidArgumentException If one or more of the conditions above are not respected.
     */
    protected function checkType($value) {
        parent::checkType($value);
        
        if (!$value instanceof OutcomeVariable) {
            $className = get_class($value);
            $msg = "The DurationStore only aims at storing OutcomeVariable objects, ${className} object given.";
            throw new InvalidArgumentException($msg);
        }
        
        if (($bt = $value->getBaseType()) !== BaseType::DURATION) {
            $baseTypeName = BaseType::getNameByConstant($bt);
            $msg = "The DurationStore only aims at storing OutcomeVariable objects with a 'duration' baseType, ";
            $msg .= "'${baseTypeName}' baseType given ";
            
            $id = $value->getIdentifier();
            $msg .= "for variable '${id}'.";
            
            throw new InvalidArgumentException($msg);
        }
        
        if (($bt = $value->getCardinality()) !== Cardinality::SINGLE) {
            $cardinalityName = Cardinality::getNameByConstant($bt);
            $msg = "The DurationStore only aims at storing OutcomeVariable objects with a 'single' cardinality, ";
            $msg .= "'${cardinalityName}' cardinality given ";
            
            $id = $value->getIdentifier();
            $msg .= "for variable '${id}'.";
            
            throw new InvalidArgumentException($msg);
        }
        
    }
}