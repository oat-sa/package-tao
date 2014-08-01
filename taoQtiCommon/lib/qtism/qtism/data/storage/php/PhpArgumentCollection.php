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

namespace qtism\data\storage\php;

use qtism\common\collections\AbstractCollection;
use \InvalidArgumentException;

/**
 * This class aims at storing PhpArgument objects.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PhpArgumentCollection extends AbstractCollection {
    
    /**
     * Checks wether $value is an instance of PhpArgumentCollection.
     * 
     * @throws InvalidArgumentException If $value is not an instance of PhpArgumentCollection.
     */
    protected function checkType($value) {
        if (!$value instanceof PhpArgument) {
            $msg = "A PhpArgumentCollection only accepts PhpArgument objects to be stored.";
            throw new InvalidArgumentException($msg);
        }
    }
}