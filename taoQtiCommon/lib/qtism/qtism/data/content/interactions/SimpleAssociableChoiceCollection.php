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

namespace qtism\data\content\interactions;

use qtism\data\QtiComponentCollection;
use \InvalidArgumentException;

/**
 * A specialization of AbstractCollection aiming at storing SimpleAssociableChoice objects
 * only.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SimpleAssociableChoiceCollection extends QtiComponentCollection {
    
    /**
     * Check if $value is an instance of SimpleAssociableChoice.
     * 
     * @throws InvalidArgumentException If $value is not an instance of SimpleAssociableChoice.
     */
    protected function checkType($value) {
        if (!$value instanceof SimpleAssociableChoice) {
            $msg = "SimpleAssociableChoiceCollection objects only accepts to store SimpleAssociableChoice objects.";
            throw new InvalidArgumentException($msg);
        }
    }
}