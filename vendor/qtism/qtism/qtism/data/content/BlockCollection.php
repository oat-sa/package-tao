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

namespace qtism\data\content;

use qtism\data\QtiComponentCollection;
use \InvalidArgumentException;

/**
 * A specialization of the QtiComponentCollection class aiming at restricting
 * the storage to Block objects only.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class BlockCollection extends QtiComponentCollection {
    
    /**
     * Check whether $value is an instance of lock.
     * 
     * @throws InvalidArgumentException If $value is not an Block object.
     */
    protected function checkType($value) {
        if (!$value instanceof Block) {
            $msg = "BlockCollection objects only accept to store Block objects.";
            throw new InvalidArgumentException($msg);
        }
    }
}