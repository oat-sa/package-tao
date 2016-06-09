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

namespace qtism\common\beans;

use qtism\common\collections\AbstractCollection;
use \InvalidArgumentException;

/**
 * A specialization of AbstractCollection that restricts the object storage
 * to BeanParameter objects only.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class BeanParameterCollection extends AbstractCollection {
    
    /**
     * Checks whether $value is an instance of BeanParameter.
     * 
     * @throws InvalidArgumentException If $value is not an instance of BeanParameter.
     */
    protected function checkType($value) {
        if (!$value instanceof BeanParameter) {
            $msg = "The BeanParameterCollection class only accepts BeanParameter objects to be stored.";
            throw new InvalidArgumentException($msg);
        }
    }
}